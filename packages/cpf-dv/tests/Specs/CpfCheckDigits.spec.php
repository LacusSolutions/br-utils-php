<?php

declare(strict_types=1);

namespace Lacus\BrUtils\Cpf\Tests\Specs;

use Lacus\BrUtils\Cpf\CpfCheckDigits;
use Lacus\BrUtils\Cpf\Exceptions\CpfCheckDigitsInputInvalidException;
use Lacus\BrUtils\Cpf\Exceptions\CpfCheckDigitsInputLengthException;
use Lacus\BrUtils\Cpf\Exceptions\CpfCheckDigitsInputTypeError;
use Lacus\BrUtils\Cpf\Tests\Mocks\CpfCheckDigitsWithCalculateSpy;

describe('CpfCheckDigits', function () {
    /** @var list<string[]> */
    $testCases = [
        ['054496519', '05449651910'],
        ['965376562', '96537656206'],
        ['339670768', '33967076806'],
        ['623855638', '62385563827'],
        ['582286009', '58228600950'],
        ['935218534', '93521853403'],
        ['132115335', '13211533508'],
        ['492602225', '49260222575'],
        ['341428925', '34142892533'],
        ['727598627', '72759862720'],
        ['478880583', '47888058396'],
        ['336636977', '33663697797'],
        ['859249430', '85924943038'],
        ['306829569', '30682956961'],
        ['443539643', '44353964321'],
        ['439709507', '43970950783'],
        ['557601402', '55760140221'],
        ['951159579', '95115957922'],
        ['671669104', '67166910496'],
        ['627571100', '62757110004'],
        ['515930555', '51593055560'],
        ['303472731', '30347273130'],
        ['728843365', '72884336508'],
        ['523667424', '52366742479'],
        ['513362164', '51336216476'],
        ['427546407', '42754640797'],
        ['880696512', '88069651237'],
        ['571430852', '57143085227'],
        ['561416205', '56141620540'],
        ['769627950', '76962795050'],
        ['416603400', '41660340063'],
        ['853803696', '85380369634'],
        ['484667676', '48466767657'],
        ['058588388', '05858838820'],
        ['862778820', '86277882007'],
        ['047126827', '04712682752'],
        ['881801816', '88180181677'],
        ['932053118', '93205311884'],
        ['029783613', '02978361379'],
        ['950189877', '95018987766'],
        ['842528992', '84252899206'],
        ['216901618', '21690161809'],
        ['110478730', '11047873001'],
        ['032967591', '03296759158'],
        ['700386565', '70038656531'],
        ['929036812', '92903681287'],
        ['750529972', '75052997272'],
        ['481063058', '48106305872'],
        ['307721932', '30772193282'],
        ['994799423', '99479942364'],
    ];

    $repeatedDigitInputs = [
        '111111111',
        '222222222',
        '333333333',
        '444444444',
        '555555555',
        '666666666',
        '777777777',
        '888888888',
        '999999999',
        '000000000',
        ['111', '111', '111'],
        ['222', '222', '222'],
        ['333', '333', '333'],
        ['444', '444', '444'],
        ['555', '555', '555'],
        ['666', '666', '666'],
        ['777', '777', '777'],
        ['888', '888', '888'],
        ['999', '999', '999'],
        ['000', '000', '000'],
        ['1', '1', '1', '1', '1', '1', '1', '1', '1'],
        ['2', '2', '2', '2', '2', '2', '2', '2', '2'],
        ['3', '3', '3', '3', '3', '3', '3', '3', '3'],
        ['4', '4', '4', '4', '4', '4', '4', '4', '4'],
        ['5', '5', '5', '5', '5', '5', '5', '5', '5'],
        ['6', '6', '6', '6', '6', '6', '6', '6', '6'],
        ['7', '7', '7', '7', '7', '7', '7', '7', '7'],
        ['8', '8', '8', '8', '8', '8', '8', '8', '8'],
        ['9', '9', '9', '9', '9', '9', '9', '9', '9'],
        ['0', '0', '0', '0', '0', '0', '0', '0', '0'],
    ];

    describe('constructor', function () use ($repeatedDigitInputs) {
        describe('when given invalid input type', function () {
            it('throws CpfCheckDigitsInputTypeError for integer input', function () {
                /** @var mixed $invalid */
                $invalid = 12345678901;

                expect(fn () => new CpfCheckDigits($invalid))->toThrow(CpfCheckDigitsInputTypeError::class);
            });

            it('throws CpfCheckDigitsInputTypeError for null input', function () {
                /** @var mixed $invalid */
                $invalid = null;

                expect(fn () => new CpfCheckDigits($invalid))->toThrow(CpfCheckDigitsInputTypeError::class);
            });

            it('throws CpfCheckDigitsInputTypeError for object input', function () {
                /** @var mixed $invalid */
                $invalid = (object) ['cpf' => '12345678901'];

                expect(fn () => new CpfCheckDigits($invalid))->toThrow(CpfCheckDigitsInputTypeError::class);
            });

            it('throws CpfCheckDigitsInputTypeError for array of numbers', function () {
                /** @var mixed $invalid */
                $invalid = [1, 2, 3, 4, 5, 6, 7, 8, 9];

                expect(fn () => new CpfCheckDigits($invalid))->toThrow(CpfCheckDigitsInputTypeError::class);
            });

            it('throws CpfCheckDigitsInputTypeError for mixed array types', function () {
                /** @var mixed $invalid */
                $invalid = [1, '2', 3, '4', 5];

                expect(fn () => new CpfCheckDigits($invalid))->toThrow(CpfCheckDigitsInputTypeError::class);
            });
        });

        describe('when given invalid input length', function () {
            it('throws CpfCheckDigitsInputLengthException for empty string', function () {
                /** @var mixed $invalid */
                $invalid = '';

                expect(fn () => new CpfCheckDigits($invalid))->toThrow(CpfCheckDigitsInputLengthException::class);
            });

            it('throws CpfCheckDigitsInputLengthException for empty array', function () {
                expect(fn () => new CpfCheckDigits([]))->toThrow(CpfCheckDigitsInputLengthException::class);
            });

            it('throws CpfCheckDigitsInputLengthException for non-numeric string', function () {
                /** @var mixed $invalid */
                $invalid = 'abcdefghij';

                expect(fn () => new CpfCheckDigits($invalid))->toThrow(CpfCheckDigitsInputLengthException::class);
            });

            it('throws CpfCheckDigitsInputLengthException for string with 8 digits', function () {
                /** @var mixed $invalid */
                $invalid = '12345678';

                expect(fn () => new CpfCheckDigits($invalid))->toThrow(CpfCheckDigitsInputLengthException::class);
            });

            it('throws CpfCheckDigitsInputLengthException for string with 12 digits', function () {
                /** @var mixed $invalid */
                $invalid = '123456789100';

                expect(fn () => new CpfCheckDigits($invalid))->toThrow(CpfCheckDigitsInputLengthException::class);
            });

            it('throws CpfCheckDigitsInputLengthException for string array with 8 digits', function () {
                /** @var mixed $invalid */
                $invalid = ['1', '2', '3', '4', '5', '6', '7', '8'];

                expect(fn () => new CpfCheckDigits($invalid))->toThrow(CpfCheckDigitsInputLengthException::class);
            });

            it('throws CpfCheckDigitsInputLengthException for string array with 12 digits', function () {
                /** @var mixed $invalid */
                $invalid = ['0', '5', '4', '4', '9', '6', '5', '1', '9', '1', '0', '0'];

                expect(fn () => new CpfCheckDigits($invalid))->toThrow(CpfCheckDigitsInputLengthException::class);
            });
        });

        describe('when given repeated digits', function () use ($repeatedDigitInputs) {
            it('throws CpfCheckDigitsInputInvalidException for repeated-digit input', function (string|array $input) {
                expect(fn () => new CpfCheckDigits($input))->toThrow(CpfCheckDigitsInputInvalidException::class);
            })->with(array_map(static fn (string|array $item): array => [$item], $repeatedDigitInputs));
        });
    });

    describe('first digit', function () use ($testCases) {
        $firstDigitTestCases = [];

        foreach ($testCases as [$input, $expectedFull]) {
            $firstDigitTestCases[] = [$input, substr($expectedFull, -2, 1)];
        }

        describe('when input is a string', function () use ($firstDigitTestCases) {
            it('returns `$expected` as first digit for `$input`', function (string $input, string $expected) {
                $cpfCheckDigits = new CpfCheckDigits($input);

                expect($cpfCheckDigits->first)->toBe($expected);
            })->with($firstDigitTestCases);
        });

        describe('when input is an array of strings', function () use ($firstDigitTestCases) {
            it('returns `$expected` as first digit for `$input`', function (string $input, string $expected) {
                $cpfCheckDigits = new CpfCheckDigits(str_split($input, 1));

                expect($cpfCheckDigits->first)->toBe($expected);
            })->with($firstDigitTestCases);
        });

        describe('when accessing digits multiple times', function () {
            it('returns cached values on subsequent calls', function () {
                $cpfCheckDigits = new CpfCheckDigitsWithCalculateSpy('123456789');

                $results = [];
                $results[] = $cpfCheckDigits->first;
                $results[] = $cpfCheckDigits->first;
                $results[] = $cpfCheckDigits->first;
                $uniqueResults = array_unique($results);

                expect($uniqueResults)->toHaveLength(1);
                expect($cpfCheckDigits->calculateCallCount)->toBe(1);
            });
        });
    });

    describe('second digit', function () use ($testCases) {
        $secondDigitTestCases = [];

        foreach ($testCases as [$input, $expectedFull]) {
            $secondDigitTestCases[] = [$input, substr($expectedFull, -1)];
        }

        describe('when input is a string', function () use ($secondDigitTestCases) {
            it('returns `$expected` as second digit for `$input`', function (string $input, string $expected) {
                $cpfCheckDigits = new CpfCheckDigits($input);

                expect($cpfCheckDigits->second)->toBe($expected);
            })->with($secondDigitTestCases);
        });

        describe('when input is an array of strings', function () use ($secondDigitTestCases) {
            it('returns `$expected` as second digit for `$input`', function (string $input, string $expected) {
                $cpfCheckDigits = new CpfCheckDigits(str_split($input, 1));

                expect($cpfCheckDigits->second)->toBe($expected);
            })->with($secondDigitTestCases);
        });

        describe('when accessing digits multiple times', function () {
            it('returns cached values on subsequent calls', function () {
                $cpfCheckDigits = new CpfCheckDigitsWithCalculateSpy('123456789');

                $results = [];
                $results[] = $cpfCheckDigits->second;
                $results[] = $cpfCheckDigits->second;
                $results[] = $cpfCheckDigits->second;
                $uniqueResults = array_unique($results);

                expect($uniqueResults)->toHaveLength(1);
                expect($cpfCheckDigits->calculateCallCount)->toBe(2);
            });
        });
    });

    describe('both digits', function () use ($testCases) {
        $bothDigitsTestCases = [];

        foreach ($testCases as [$input, $expectedFull]) {
            $bothDigitsTestCases[] = [$input, substr($expectedFull, -2)];
        }

        describe('when input is a string', function () use ($bothDigitsTestCases) {
            it('returns `$expected` as check digits for `$input`', function (string $input, string $expected) {
                $cpfCheckDigits = new CpfCheckDigits($input);

                expect($cpfCheckDigits->both)->toBe($expected);
            })->with($bothDigitsTestCases);
        });

        describe('when input is an array of strings', function () use ($bothDigitsTestCases) {
            it('returns `$expected` as check digits for `$input`', function (string $input, string $expected) {
                $cpfCheckDigits = new CpfCheckDigits(str_split($input, 1));

                expect($cpfCheckDigits->both)->toBe($expected);
            })->with($bothDigitsTestCases);
        });
    });

    describe('actual CPF string', function () use ($testCases) {
        describe('when input is a string', function () {
            it('returns the respective 11-character string for CPF', function () {
                $cpfCheckDigits = new CpfCheckDigits('123456789');

                expect($cpfCheckDigits->cpf)->toBe('12345678909');
            });
        });

        describe('when input is an array of grouped digits string', function () {
            it('returns the respective 11-character string for CPF', function () {
                $cpfCheckDigits = new CpfCheckDigits(['123', '456', '789']);

                expect($cpfCheckDigits->cpf)->toBe('12345678909');
            });
        });

        describe('when input is an array of individual digits string', function () {
            it('returns the respective 11-character string for CPF', function () {
                $cpfCheckDigits = new CpfCheckDigits(['1', '2', '3', '4', '5', '6', '7', '8', '9']);

                expect($cpfCheckDigits->cpf)->toBe('12345678909');
            });
        });

        describe('when validating all test cases', function () use ($testCases) {
            it('returns `$expected` for `$input`', function (string $input, string $expected) {
                $cpfCheckDigits = new CpfCheckDigits($input);

                expect($cpfCheckDigits->cpf)->toBe($expected);
            })->with($testCases);
        });
    });

    describe('edge cases', function () {
        describe('when input is a formatted CPF string', function () {
            it('correctly parses and calculates check digits', function () {
                $cpfCheckDigits = new CpfCheckDigits('123.456.789');

                expect($cpfCheckDigits->cpf)->toBe('12345678909');
            });
        });

        describe('when input already contains check digits', function () {
            it('ignores provided check digits and calculates ones correctly', function () {
                $cpfCheckDigits = new CpfCheckDigits('12345678910');

                expect($cpfCheckDigits->first)->toBe('0');
                expect($cpfCheckDigits->second)->toBe('9');
                expect($cpfCheckDigits->cpf)->toBe('12345678909');
            });
        });
    });
});
