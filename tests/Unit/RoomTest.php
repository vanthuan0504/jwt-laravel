<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Room;

class RoomTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_room_has() {
        $room = new Room(["Jack", "Peter", "Amy"]);
        $this->assertTrue($room->has("Jack"));
        $this->assertFalse($room->has('Eric'));
    }

    public function test_root_add() {
        $room = new Room(['Jack']);
        $this->assertContains("Peter", $room->add('Peter'));
    }

    public function test_room_remove() {
        $room = new Room(["Jack", "Peter"]);
        $this->assertCount(1, $room->remove("Peter"));
    }
}
