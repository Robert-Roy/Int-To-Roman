<?php

namespace Robert;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;

class Convert extends Command {

    public function configure() {
        $this->setName("convert")
                ->setDescription("Converts an integer to its roman numeral equivalent")
                ->addArgument("Number", InputArgument::REQUIRED);
    }

    public function execute(InputInterface $input, OutputInterface $output) {
        $inputNumber = $input->getArgument('Number');
        // Fail on any non-integer input
        if (filter_var($inputNumber, FILTER_VALIDATE_INT) === false) {
            $output->writeln("<error>Input is not an integer. Please enter an integer</error>");
            exit(1);
        }
        if ($inputNumber < 1) {
            $output->writeln("<error>There is no Roman Numeral below I (1).</error>");
        }
        $output->writeln("<info>{$this->intToRoman($inputNumber)}</info>");
    }

    private function intToRoman($inputNumber) {
        $roman = "";
        $romanCharacter = [
            1 => "I",
            5 => "V",
            10 => "X",
            50 => "L",
            100 => "C",
            500 => "D",
            1000 => "M"];
        $firstDigit = 1;
        for ($i = 1000; $i >= 1 && $inputNumber >= 1;) {
            //Useful lines for debugging
            //echo "Checking " . $i . " against " . $inputNumber . "\n";
            //echo $roman . "\n";
            if ($inputNumber / $i >= 1) {
                // if the input number is greater than the current numeral, append it to the end
                // and reduce current value by current numeral.
                $roman = $roman . $romanCharacter[$i];
                $inputNumber -= $i;
            } elseif ($firstDigit === 1) {
                // If the first digit of a numeral is one, and the remaining $inputNumber is
                // more than 90% of it, the correct response is 1/10th the current numeral, then the
                // current numeral.
                if ($inputNumber / $i >= .9) {
                    $inputNumber -= $i * .9;
                    $roman = $roman . $romanCharacter[$i / 10] . $romanCharacter[$i];
                }
                // At this point, we need to move down to the next roman numeral, which will always be
                // 1/2 of the current numeral since the first digit is 1. The first digit will now be 5.
                $i = $i / 2;
                $firstDigit = 5;
            } elseif ($firstDigit === 5) {
                // If the first digit of a numeral is 5, and the remaining $inputNumber is
                // more than 90% of it, the correct response is 1/5th the current numeral, then the
                // current numeral.
                if ($inputNumber / $i >= .8) {
                    $inputNumber -= $i * .8;
                    $roman = $roman . $romanCharacter[$i / 5] . $romanCharacter[$i];
                }
                // At this point, we need to move down to the next roman numeral, which will always be
                // 1/5th of the current numeral since the first digit is five. The first digit will now be 1.
                $i = $i / 5;
                $firstDigit = 1;
            }
        }
        return $roman;
    }

}
