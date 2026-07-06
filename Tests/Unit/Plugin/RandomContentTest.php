<?php

declare(strict_types=1);

namespace Amazing\GhRandomcontent\Tests\Unit\Plugin;

use Amazing\GhRandomcontent\Plugin\RandomContent;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class RandomContentTest extends UnitTestCase
{
    #[Test]
    #[DataProvider('provideContentUIDs')]
    public function selectContentUIDsTest(
        array $content_ids,
        int $count,
        array $expected_result,
    ): void {
        $reflectionMethod = new \ReflectionMethod(RandomContent::class, 'selectContentUIDs');
        $result = $reflectionMethod->invokeArgs(
            new RandomContent(),
            [
                'content_ids' => $content_ids,
                'count'       => $count,
            ],
        );
        self::assertContains($result, $expected_result);
    }

    /**
     * Every array splits into
     * - input array,
     * - count of elements in the result array,
     * - all possible result arrays
     */
    public static function provideContentUIDs(): array
    {
        return [
            'more elements than count' => [
                [
                    0 => ['uid' => 1],
                    1 => ['uid' => 23],
                    2 => ['uid' => 456],
                ],
                2,
                [
                    [0, 1],
                    [0, 2],
                    [1, 0],
                    [1, 2],
                    [2, 0],
                    [2, 1],
                ],
            ],
            'less elements than count' => [
                [
                    0 => ['uid' => 42],
                ],
                3,
                [
                    [0],
                ],
            ],
            'count 1'                  => [
                [
                    0 => ['uid' => 1],
                    1 => ['uid' => 23],
                    2 => ['uid' => 456],
                ],
                1,
                [
                    [0],
                    [1],
                    [2],
                ],
            ],
        ];
    }
}
