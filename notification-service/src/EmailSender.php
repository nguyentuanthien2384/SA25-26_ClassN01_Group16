<?php

namespace NotificationService;

use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;

class EmailSender
{
    private Mailer $mailer;
    private LoggerInterface $logger;
    private array $config;

    public function __construct(LoggerInterface $logger, array $config)
    {
        $this->logger = $logger;
        $this->config = $config;
        
        // Create SMTP transport
        $smtp = $config['smtp'];
        $dsn = sprintf(
            '%s://%s:%s@%s:%d',
            $smtp['encryption'] === 'ssl' ? 'smtps' : 'smtp',
            urlencode($smtp['username']),
            urlencode($smtp['password']),
            $smtp['host'],
            $smtp['port']
        );
        
        $transport = Transport::fromDsn($dsn);
        $this->mailer = new Mailer($transport);
    }

    /**
     * Handle email sending based on event type
     */
    public function handleEvent(array $eventData): void
    {
        try {
            $eventType = $eventData['event_type'] ?? null;
            $payload = $eventData['payload'] ?? [];

            $this->logger->info("Processing event", [
                'event_type' => $eventType,
                'aggregate_id' => $eventData['aggregate_id'] ?? null,
            ]);

            switch ($eventType) {
                case 'OrderPlaced':
                    $this->sendOrderConfirmation($payload);
                    break;

                case 'UserRegistered':
                    $this->sendWelcomeEmail($payload);
                    break;

                case 'PaymentSucceeded':
                    $this->sendPaymentConfirmation($payload);
                    break;

                default:
                    $this->logger->warning("Unknown event type", ['event_type' => $eventType]);
            }

        } catch (\Exception $e) {
            $this->logger->error("Failed to handle event", [
                'error' => $e->getMessage(),
                'event_data' => $eventData,
            ]);
            throw $e;
        }
    }

    /**
     * Send order confirmation email
     */
    private function sendOrderConfirmation(array $payload): void
    {
        $transactionId = $payload['transaction_id'] ?? 'N/A';
        $total = number_format($payload['total'] ?? 0, 0, ',', '.') . ' VND';
        $userEmail = $payload['user_email'] ?? null;

        if (!$userEmail) {
            $this->logger->warning("No user email for order confirmation", ['transaction_id' => $transactionId]);
            return;
        }

        $email = (new Email())
            ->from($this->config['smtp']['from']['email'])
            ->to($userEmail)
            ->subject('Xác nhận đơn hàng #' . $transactionId)
            ->html($this->buildOrderTemplate($transactionId, $total, $payload));

        $this->mailer->send($email);

        $this->logger->info("Order confirmation email sent", [
            'transaction_id' => $transactionId,
            'to' => $userEmail,
        ]);
    }

    /**
     * Send welcome email
     */
    private function sendWelcomeEmail(array $payload): void
    {
        $email = (new Email())
            ->from($this->config['smtp']['from']['email'])
            ->to($payload['email'] ?? '')
            ->subject('Chào mừng đến với Web Bán Hàng')
            ->html($this->buildWelcomeTemplate($payload));

        $this->mailer->send($email);

        $this->logger->info("Welcome email sent", ['to' => $payload['email'] ?? 'unknown']);
    }

    /**
     * Send payment confirmation email
     */
    private function sendPaymentConfirmation(array $payload): void
    {
        $email = (new Email())
            ->from($this->config['smtp']['from']['email'])
            ->to($payload['user_email'] ?? '')
            ->subject('Thanh toán thành công #' . ($payload['transaction_id'] ?? 'N/A'))
            ->html($this->buildPaymentTemplate($payload));

        $this->mailer->send($email);

        $this->logger->info("Payment confirmation email sent", [
            'transaction_id' => $payload['transaction_id'] ?? 'N/A',
        ]);
    }

    /**
     * Build order confirmation email template
     */
    private function buildOrderTemplate(string $orderId, string $total, array $payload): string
    {
        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #4f46e5; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f9f9f9; }
        .footer { text-align: center; padding: 20px; font-size: 12px; color: #666; }
        .button { background: #4f46e5; color: white; padding: 10px 20px; text-decoration: none; display: inline-block; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Xác Nhận Đơn Hàng</h1>
        </div>
        <div class="content">
            <h2>Đơn hàng #{$orderId}</h2>
            <p>Cảm ơn bạn đã đặt hàng tại Web Bán Hàng!</p>
            <p><strong>Tổng tiền:</strong> {$total}</p>
            <p><strong>Phương thức thanh toán:</strong> {$payload['payment_method']}</p>
            <p>Chúng tôi sẽ xử lý đơn hàng của bạn trong thời gian sớm nhất.</p>
            <p><a href="#" class="button">Xem chi tiết đơn hàng</a></p>
        </div>
        <div class="footer">
            <p>© 2026 Web Bán Hàng. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Build welcome email template
     */
    private function buildWelcomeTemplate(array $payload): string
    {
        $name = $payload['name'] ?? 'Khách hàng';
        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #4f46e5; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f9f9f9; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Chào mừng {$name}!</h1>
        </div>
        <div class="content">
            <p>Cảm ơn bạn đã đăng ký tài khoản tại Web Bán Hàng.</p>
            <p>Hãy khám phá hàng ngàn sản phẩm chất lượng của chúng tôi!</p>
        </div>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Build payment confirmation template
     */
    private function buildPaymentTemplate(array $payload): string
    {
        $orderId = $payload['transaction_id'] ?? 'N/A';
        $total = number_format($payload['total'] ?? 0, 0, ',', '.') . ' VND';
        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #10b981; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f9f9f9; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✅ Thanh Toán Thành Công</h1>
        </div>
        <div class="content">
            <h2>Đơn hàng #{$orderId}</h2>
            <p>Thanh toán của bạn đã được xác nhận thành công!</p>
            <p><strong>Số tiền:</strong> {$total}</p>
            <p>Đơn hàng của bạn đang được xử lý.</p>
        </div>
    </div>
</body>
</html>
HTML;
    }
}
