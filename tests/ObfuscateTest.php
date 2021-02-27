<?php

class ObfuscateTest extends Base
{

    public const AFTER = "/after";
    public const BEFORE = "/before";
    public const EXPECTED = "/expected";

    public function testObfuscate(): void
    {
        $beforePath = __DIR__ . self::BEFORE;
        $afterPath = __DIR__ . self::AFTER;
        $expectedPath = __DIR__ . self::EXPECTED;
        $configPath = __DIR__ . "/config.yml";

        shell_exec("cd {$beforePath}; ../../bin/obfuscate obfuscate . {$afterPath} --config={$configPath}");
        $expectedFileNames = scandir($expectedPath);
        foreach ($expectedFileNames as $fileName) {
            if ($fileName === '.' || $fileName === '..') {
                continue;
            }

            $afterFile = "{$afterPath}/{$fileName}";
            $expectedFile = "{$expectedPath}/{$fileName}";

            if (!file_exists($afterFile)) {
                self::fail("{$afterPath}/{$fileName} not found");
            }
            self::assertFileEquals($expectedFile, $afterFile);
        }
    }

    protected function tearDown(): void
    {
        $afterPath = __DIR__ . self::AFTER;

        $files = glob($afterPath . "/*");
        foreach ($files as $file) {
            unlink($file);
        }
        rmdir($afterPath);
    }
}