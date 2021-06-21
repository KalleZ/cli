<?php

declare(strict_types=1);

namespace Cli\Controllers;

use Cli\Attributes\Controller;
use Cli\Attributes\Command;
use Cli\Attributes\Help;
use Cli\BaseController;
use Cli\Color;
use Cli\Cursor;
use Cli\Text;

#[Controller(group: 'test')]
class TestController extends BaseController
{
    #[Command(name: 'greeting')]
    #[Help(text: 'A basic "Hello World" program')]
    public function helloWorld(): void
    {
        echo 'Hello World';
    }

    private static function getColors(): array
    {
        return [
            Color::BLACK,
            Color::RED,
            Color::GREEN,
            Color::YELLOW,
            Color::BLUE,
            Color::MAGENTA,
            Color::CYAN,
            Color::WHITE,
            Color::DEFAULT,
        ];
    }

    private static function getBrightColors(): array
    {
        return [
            Color::BRIGHT_BLACK,
            Color::BRIGHT_RED,
            Color::BRIGHT_GREEN,
            Color::BRIGHT_YELLOW,
            Color::BRIGHT_BLUE,
            Color::BRIGHT_MAGENTA,
            Color::BRIGHT_CYAN,
            Color::BRIGHT_WHITE,
            Color::DEFAULT,
        ];
    }

    private static function getRandomColor(): Color
    {
        $colors = self::getColors();

        return $colors[\array_rand($colors)];
    }

    #[Command(name: 'color')]
    #[Help(text: 'VGA color playground')]
    public function color(string $text = 'php'): void
    {
        $colors = self::getColors();

        foreach ($colors as $textColor) {
            foreach ($colors as $backgroundColor) {
                $this->stdout->write(
                    new Text(
                        text: $text,
                        color: $textColor,
                        backgroundColor: $backgroundColor,
                    ),
                );

                $this->stdout->write(' ');
            }

            $this->stdout->writeEol();
        }
    }

    #[Command(name: 'box')]
    public function colorBox(string $text = 'php', bool $multi = false): void
    {
        if ($multi) {
            $text = [
                $text,
                $text,
                $text,
            ];
        }

        $this->stdout->writeSuccessBox($text);
        $this->stdout->writeEol();
        $this->stdout->writeErrorBox($text);
    }

    #[Command(name: 'bork')]
    public function bork(bool $t = false): void
    {
        echo 'test';
    }

    #[Command(name: 'ask')]
    public function askSimple(): void
    {
        if ($this->stdin->askYesNo('Do you like flowers?')) {
            $this->stdout->writeSuccessBox('Great!');
        } else {
            $this->stdout->writeErrorBox('Oh well...');
        }
    }

    #[Command(name: 'rainbow')]
    public function rainbow(string $input = 'php', int $times = 5, bool $background = false, bool $text = true): void
    {
        for($i = 0; $i < $times; $i++) {
            $output = '';

            foreach (\str_split($input) as $char) {
                $output .= new Text(
                    $char,
                    $text ? self::getRandomColor() : Color::DEFAULT,
                    $background ? self::getRandomColor() : Color::DEFAULT,
                );
            }

            $this->stdout->writeLine($output);
        }
    }

    #[Command(name: 'info')]
    public function blueBox(int $lineLength = null): void
    {
        if ($lineLength !== null) {
            $this->stdout->setLineLength($lineLength);
        }

        $this->stdout->writeBox(
            'This is an informative dialog',
            Color::WHITE,
            Color::BLUE,
        );
    }

    #[Command(name: 'bright')]
    public function brightTest(): void
    {
        foreach ([self::getColors(), self::getBrightColors()] as $colors) {
            foreach ($colors as $textColor) {
                foreach ($colors as $backgroundColor) {
                    $this->stdout->write(
                        new Text(
                            text: 'PHP',
                            color: $textColor,
                            backgroundColor: $backgroundColor,
                        ),
                    );

                    $this->stdout->write(' ');
                }

                $this->stdout->writeEol();
            }
        }
    }

    #[Command(name: 'cursor')]
    public function cursorTest(int $sleepTimer = 3): void
    {
        $commands = [
            'Cursor stop blinking' => Cursor::NOBLINK,
            'Cursor start blinking' => Cursor::BLINK,
            'Cursor hide' => Cursor::HIDE,
            'Cursor show' => Cursor::SHOW,
            'Cursor underline' => Cursor::UNDERLINE,
            'Cursor beam' => Cursor::BEAM,
            'Cursor block' => Cursor::BLOCK,
        ];

        foreach ($commands as $label => $cursor) {
            $this->stdout->writeLine($label);
            $this->stdout->cursor($cursor);

            \sleep($sleepTimer);
        }
    }

    private function scrollFillerLines(int $lines): void
    {
        $filler = '|' . \str_repeat('---', 30) . '|';

        for ($i = 1; $lines >= $i; $i++) {
            $this->stdout->writeLineF(
                '%s Line %d',
                $filler,
                $i,
            );
        }
    }

    #[Command('scroll')]
    public function scrollTest(int $lines = 50): void
    {
        $this->scrollFillerLines($lines);
        $this->stdout->writeLine('Pushing 20 lines...');

        \sleep(2);

        $this->stdout->scrollUp(20);

        \sleep(2);

        $this->scrollFillerLines($lines);
        $this->stdout->writeLine('Reversing 20 lines...');

        \sleep(2);

        $this->stdout->scrollDown(20);
    }

    #[Command(name: 'typewriter')]
    public function typeWriter(string $text, bool $bytesMode = false): void
    {
        $this->stdout->cursor(Cursor::BLINK);
        \usleep(750000);

        if ($bytesMode) {
            foreach (\str_split($text) as $char) {
                $this->stdout->write($char);
                \usleep(50000);
            }
        } else {
            foreach (\str_word_count($text, 1) as $word) {
                $this->stdout->write($word . ' ');
                \usleep(750000);
            }
        }

        $this->stdout->writeEol();
    }

    public function __invoke(float|null $v1, int $v2 = 2, string $v3 = null, float $v4 = 1.234): void
    {
        echo 'phpfi', PHP_EOL;

        var_dump($v1, $v2, $v3, $v4);
    }
}