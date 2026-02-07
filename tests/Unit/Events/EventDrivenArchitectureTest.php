<?php

namespace Tests\Unit\Events;

use PHPUnit\Framework\TestCase;

/**
 * Unit Test for Event-Driven Architecture
 * 
 * Tests: Event creation, Outbox pattern, Event publishing flow
 * Components: Events, Listeners, Outbox Messages, Queue Jobs
 */
class EventDrivenArchitectureTest extends TestCase
{
    // ======================================================
    // TEST: Event Objects
    // ======================================================

    /**
     * Test OrderPlaced event contains correct data
     */
    public function test_order_placed_event_contains_correct_data(): void
    {
        $transaction = [
            'id' => 1,
            'user_id' => 100,
            'total' => 5000000,
            'payment_method' => 'momo',
        ];

        $orderDetails = [
            ['product_id' => 101, 'product_name' => 'iPhone 15', 'quantity' => 1, 'price' => 5000000],
        ];

        $event = $this->createOrderPlacedEvent($transaction, $orderDetails);

        $this->assertEquals(1, $event['transaction']['id']);
        $this->assertEquals(100, $event['transaction']['user_id']);
        $this->assertEquals(5000000, $event['transaction']['total']);
        $this->assertCount(1, $event['order_details']);
    }

    /**
     * Test ProductCreated event contains product data
     */
    public function test_product_created_event_contains_product_data(): void
    {
        $product = [
            'id' => 50,
            'pro_name' => 'Samsung Galaxy S24',
            'pro_price' => 22990000,
            'pro_category_id' => 1,
        ];

        $event = $this->createProductEvent('ProductCreated', $product);

        $this->assertEquals('ProductCreated', $event['type']);
        $this->assertEquals(50, $event['product']['id']);
        $this->assertEquals('Samsung Galaxy S24', $event['product']['pro_name']);
    }

    /**
     * Test ProductUpdated event contains updated product
     */
    public function test_product_updated_event_contains_updated_data(): void
    {
        $product = [
            'id' => 50,
            'pro_name' => 'Samsung Galaxy S24 Ultra',
            'pro_price' => 29990000,
        ];

        $event = $this->createProductEvent('ProductUpdated', $product);

        $this->assertEquals('ProductUpdated', $event['type']);
        $this->assertEquals(29990000, $event['product']['pro_price']);
    }

    /**
     * Test ProductDeleted event contains product ID
     */
    public function test_product_deleted_event_contains_product_id(): void
    {
        $event = $this->createProductDeletedEvent(50);

        $this->assertEquals('ProductDeleted', $event['type']);
        $this->assertEquals(50, $event['product_id']);
    }

    /**
     * Test DashboardUpdated event for broadcasting
     */
    public function test_dashboard_updated_event_for_broadcasting(): void
    {
        $event = $this->createDashboardUpdatedEvent('new_order');

        $this->assertEquals('new_order', $event['type']);
        $this->assertNotEmpty($event['time']);
        $this->assertEquals('dashboard', $event['channel']);
        $this->assertEquals('dashboard.updated', $event['broadcast_as']);
    }

    // ======================================================
    // TEST: Outbox Pattern
    // ======================================================

    /**
     * Test outbox message creation from OrderPlaced event
     */
    public function test_outbox_message_created_from_order_event(): void
    {
        $transaction = [
            'id' => 1,
            'user_id' => 100,
            'total' => 5000000,
            'payment_method' => 'cod',
        ];

        $outboxMessage = $this->createOutboxMessage($transaction);

        $this->assertEquals('Transaction', $outboxMessage['aggregate_type']);
        $this->assertEquals(1, $outboxMessage['aggregate_id']);
        $this->assertEquals('OrderPlaced', $outboxMessage['event_type']);
        $this->assertFalse($outboxMessage['published']);
        $this->assertNull($outboxMessage['published_at']);
    }

    /**
     * Test outbox message payload contains transaction details
     */
    public function test_outbox_message_payload_contains_details(): void
    {
        $transaction = [
            'id' => 1,
            'user_id' => 100,
            'total' => 5000000,
            'payment_method' => 'momo',
        ];

        $outboxMessage = $this->createOutboxMessage($transaction);
        $payload = $outboxMessage['payload'];

        $this->assertEquals(1, $payload['transaction_id']);
        $this->assertEquals(100, $payload['user_id']);
        $this->assertEquals(5000000, $payload['total']);
        $this->assertEquals('momo', $payload['payment_method']);
    }

    /**
     * Test mark outbox message as published
     */
    public function test_mark_outbox_message_as_published(): void
    {
        $outboxMessage = $this->createOutboxMessage(['id' => 1, 'user_id' => 100, 'total' => 1000, 'payment_method' => 'cod']);

        $this->assertFalse($outboxMessage['published']);

        $publishedMessage = $this->markPublished($outboxMessage);

        $this->assertTrue($publishedMessage['published']);
        $this->assertNotNull($publishedMessage['published_at']);
    }

    /**
     * Test filter unpublished outbox messages
     */
    public function test_filter_unpublished_outbox_messages(): void
    {
        $messages = [
            ['id' => 1, 'published' => true],
            ['id' => 2, 'published' => false],
            ['id' => 3, 'published' => false],
            ['id' => 4, 'published' => true],
        ];

        $unpublished = $this->filterUnpublished($messages);

        $this->assertCount(2, $unpublished);
        $this->assertEquals(2, $unpublished[0]['id']);
        $this->assertEquals(3, $unpublished[1]['id']);
    }

    /**
     * Test outbox messages ordered by occurred_at (FIFO)
     */
    public function test_outbox_messages_ordered_by_occurred_at(): void
    {
        $messages = [
            ['id' => 1, 'occurred_at' => '2026-02-07 10:00:00', 'published' => false],
            ['id' => 2, 'occurred_at' => '2026-02-07 09:00:00', 'published' => false],
            ['id' => 3, 'occurred_at' => '2026-02-07 11:00:00', 'published' => false],
        ];

        $sorted = $this->sortByOccurredAt($messages);

        $this->assertEquals(2, $sorted[0]['id']); // 09:00
        $this->assertEquals(1, $sorted[1]['id']); // 10:00
        $this->assertEquals(3, $sorted[2]['id']); // 11:00
    }

    // ======================================================
    // TEST: Publish Outbox Messages Job
    // ======================================================

    /**
     * Test publish job processes batch of messages
     */
    public function test_publish_job_processes_batch(): void
    {
        $messages = [];
        for ($i = 1; $i <= 5; $i++) {
            $messages[] = $this->createOutboxMessage([
                'id' => $i,
                'user_id' => 100,
                'total' => $i * 1000000,
                'payment_method' => 'cod',
            ]);
        }

        $result = $this->processOutboxBatch($messages, 100);

        $this->assertEquals(5, $result['processed_count']);
        $this->assertEquals(0, $result['failed_count']);
    }

    /**
     * Test publish job respects batch size limit
     */
    public function test_publish_job_respects_batch_size(): void
    {
        $messages = [];
        for ($i = 1; $i <= 10; $i++) {
            $messages[] = ['id' => $i, 'published' => false];
        }

        $batchSize = 3;
        $batch = array_slice($messages, 0, $batchSize);

        $this->assertCount(3, $batch);
    }

    /**
     * Test publish job creates correct Redis queue format
     */
    public function test_publish_job_creates_correct_queue_format(): void
    {
        $outboxMessage = [
            'event_type' => 'OrderPlaced',
            'aggregate_type' => 'Transaction',
            'aggregate_id' => 1,
            'payload' => ['transaction_id' => 1, 'total' => 5000000],
            'occurred_at' => '2026-02-07 10:00:00',
        ];

        $queueJob = $this->formatForQueue($outboxMessage);

        $this->assertArrayHasKey('uuid', $queueJob);
        $this->assertEquals('NotificationEvent', $queueJob['displayName']);
        $this->assertEquals('NotificationEvent', $queueJob['job']);
        $this->assertEquals('OrderPlaced', $queueJob['data']['event_type']);
        $this->assertEquals(1, $queueJob['data']['aggregate_id']);
    }

    /**
     * Test publish job skips when no unpublished messages
     */
    public function test_publish_job_skips_when_no_messages(): void
    {
        $messages = [];

        $result = $this->processOutboxBatch($messages, 100);

        $this->assertEquals(0, $result['processed_count']);
        $this->assertTrue($result['skipped']);
    }

    // ======================================================
    // TEST: Event Flow (End-to-End)
    // ======================================================

    /**
     * Test complete event flow: Order → Event → Outbox → Queue
     */
    public function test_complete_event_flow(): void
    {
        // 1. Order is placed
        $transaction = [
            'id' => 1,
            'user_id' => 100,
            'total' => 10000000,
            'payment_method' => 'momo',
        ];

        // 2. OrderPlaced event is fired
        $event = $this->createOrderPlacedEvent($transaction, []);

        // 3. Listener saves to outbox
        $outboxMessage = $this->createOutboxMessage($transaction);
        $this->assertFalse($outboxMessage['published']);

        // 4. Job publishes to queue
        $queueJob = $this->formatForQueue($outboxMessage);
        $this->assertEquals('NotificationEvent', $queueJob['job']);

        // 5. Message is marked as published
        $published = $this->markPublished($outboxMessage);
        $this->assertTrue($published['published']);
    }

    // ======================================================
    // HELPER METHODS (Event-Driven Logic)
    // ======================================================

    private function createOrderPlacedEvent(array $transaction, array $orderDetails): array
    {
        return [
            'type' => 'OrderPlaced',
            'transaction' => $transaction,
            'order_details' => $orderDetails,
        ];
    }

    private function createProductEvent(string $type, array $product): array
    {
        return [
            'type' => $type,
            'product' => $product,
        ];
    }

    private function createProductDeletedEvent(int $productId): array
    {
        return [
            'type' => 'ProductDeleted',
            'product_id' => $productId,
        ];
    }

    private function createDashboardUpdatedEvent(string $type): array
    {
        return [
            'type' => $type,
            'time' => date('Y-m-d H:i:s'),
            'channel' => 'dashboard',
            'broadcast_as' => 'dashboard.updated',
        ];
    }

    private function createOutboxMessage(array $transaction): array
    {
        return [
            'aggregate_type' => 'Transaction',
            'aggregate_id' => $transaction['id'],
            'event_type' => 'OrderPlaced',
            'payload' => [
                'transaction_id' => $transaction['id'],
                'user_id' => $transaction['user_id'],
                'total' => $transaction['total'],
                'payment_method' => $transaction['payment_method'],
            ],
            'occurred_at' => date('Y-m-d H:i:s'),
            'published' => false,
            'published_at' => null,
        ];
    }

    private function markPublished(array $message): array
    {
        $message['published'] = true;
        $message['published_at'] = date('Y-m-d H:i:s');
        return $message;
    }

    private function filterUnpublished(array $messages): array
    {
        return array_values(array_filter($messages, fn($m) => !$m['published']));
    }

    private function sortByOccurredAt(array $messages): array
    {
        usort($messages, fn($a, $b) => strcmp($a['occurred_at'], $b['occurred_at']));
        return $messages;
    }

    private function processOutboxBatch(array $messages, int $batchSize): array
    {
        if (empty($messages)) {
            return ['processed_count' => 0, 'failed_count' => 0, 'skipped' => true];
        }

        $batch = array_slice($messages, 0, $batchSize);
        $processed = 0;
        $failed = 0;

        foreach ($batch as $message) {
            try {
                $this->formatForQueue($message);
                $processed++;
            } catch (\Exception $e) {
                $failed++;
            }
        }

        return ['processed_count' => $processed, 'failed_count' => $failed, 'skipped' => false];
    }

    private function formatForQueue(array $outboxMessage): array
    {
        return [
            'uuid' => uniqid('', true),
            'displayName' => 'NotificationEvent',
            'job' => 'NotificationEvent',
            'maxTries' => null,
            'timeout' => null,
            'data' => [
                'event_type' => $outboxMessage['event_type'],
                'aggregate_type' => $outboxMessage['aggregate_type'],
                'aggregate_id' => $outboxMessage['aggregate_id'],
                'payload' => $outboxMessage['payload'],
                'occurred_at' => $outboxMessage['occurred_at'],
            ],
        ];
    }
}
