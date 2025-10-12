<?php

declare(strict_types=1);

namespace Lacus\CpfGen\Tests;

use InvalidArgumentException;
use Lacus\CpfGen\CpfGeneratorVerifierDigit;
use PHPUnit\Framework\TestCase;

class CpfGeneratorVerifierDigitTest extends TestCase
{
    private CpfGeneratorVerifierDigit $verifierDigit;

    protected function setUp(): void
    {
        $this->verifierDigit = new CpfGeneratorVerifierDigit();
    }

    public function testCalculateThrowsErrorWithTooFewDigits(): void
    {
        $cpfSequence = [0, 1, 2, 3, 0, 1, 2, 3];

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('To calculate the verifier digit, the CPF sequence must be between 9 and 10 digits long, but got 8 digits ("01230123").');
        $this->verifierDigit->calculate($cpfSequence);
    }

    public function testCalculateThrowsErrorWithTooManyDigits(): void
    {
        $cpfSequence = [0, 1, 2, 3, 4, 0, 1, 2, 3, 4, 0];

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('To calculate the verifier digit, the CPF sequence must be between 9 and 10 digits long, but got 11 digits ("01234012340").');
        $this->verifierDigit->calculate($cpfSequence);
    }

    public function testCalculateVerifierDigitsForSequencesWith9Digits(): void
    {
        $testCases = [
            '054496519' => '05449651910',
            '965376562' => '96537656206',
            '339670768' => '33967076806',
            '623855638' => '62385563827',
            '582286009' => '58228600950',
            '935218534' => '93521853403',
            '132115335' => '13211533508',
            '492602225' => '49260222575',
            '341428925' => '34142892533',
            '727598627' => '72759862720',
            '478880583' => '47888058396',
            '336636977' => '33663697797',
            '859249430' => '85924943038',
            '306829569' => '30682956961',
            '443539643' => '44353964321',
            '439709507' => '43970950783',
            '557601402' => '55760140221',
            '951159579' => '95115957922',
            '671669104' => '67166910496',
            '627571100' => '62757110004',
            '515930555' => '51593055560',
            '303472731' => '30347273130',
            '728843365' => '72884336508',
            '523667424' => '52366742479',
            '513362164' => '51336216476',
            '427546407' => '42754640797',
            '880696512' => '88069651237',
            '571430852' => '57143085227',
            '561416205' => '56141620540',
            '769627950' => '76962795050',
            '416603400' => '41660340063',
            '853803696' => '85380369634',
            '484667676' => '48466767657',
            '058588388' => '05858838820',
            '862778820' => '86277882007',
            '047126827' => '04712682752',
            '881801816' => '88180181677',
            '932053118' => '93205311884',
            '029783613' => '02978361379',
            '950189877' => '95018987766',
            '842528992' => '84252899206',
            '216901618' => '21690161809',
            '110478730' => '11047873001',
            '032967591' => '03296759158',
            '700386565' => '70038656531',
            '929036812' => '92903681287',
            '750529972' => '75052997272',
            '481063058' => '48106305872',
            '307721932' => '30772193282',
            '994799423' => '99479942364',
        ];

        foreach ($testCases as $input => $expected) {
            $inputArray = str_split((string) $input);
            $inputArray = array_map('intval', $inputArray);

            $firstDigit = $this->verifierDigit->calculate($inputArray);
            array_push($inputArray, $firstDigit);
            $secondDigit = $this->verifierDigit->calculate($inputArray);
            array_push($inputArray, $secondDigit);
            $resultString = implode('', $inputArray);

            $this->assertEquals(
                $expected,
                $resultString,
                "Input: {$input}, Expected: {$expected}, Result: {$resultString}"
            );
        }
    }
}
