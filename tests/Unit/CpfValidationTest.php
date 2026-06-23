<?php

namespace Tests\Unit;

use App\Rules\Cpf;
use PHPUnit\Framework\TestCase;

class CpfValidationTest extends TestCase
{
    private Cpf $rule;

    protected function setUp(): void
    {
        parent::setUp();
        $this->rule = new Cpf;
    }

    public function test_valid_cpf_passes()
    {
        $validCpfs = [
            '123.456.789-09',
            '12345678909',
        ];

        foreach ($validCpfs as $cpf) {
            $this->rule->validate('document_cpf', $cpf, function ($message) {
                $this->fail("CPF {$message} should be valid.");
            });
        }
        $this->assertTrue(true);
    }

    public function test_invalid_cpf_fails()
    {
        $invalidCpfs = [
            '111.111.111-11',
            '123.456.789-00',
            '123',
            'abc',
        ];

        foreach ($invalidCpfs as $cpf) {
            $called = false;
            $this->rule->validate('document_cpf', $cpf, function ($message) use (&$called) {
                $called = true;
                $this->assertEquals('O campo :attribute deve ser um CPF válido.', $message);
            });
            $this->assertTrue($called, "CPF {$cpf} should be invalid.");
        }
    }
}
