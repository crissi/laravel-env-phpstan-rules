<?php declare(strict_types = 1);

namespace Crissi\LaravelEnvPhpstanRules\Rules;

use PHPStan\Analyser\Scope;
use Illuminate\Support\Str;
use PhpParser\Node\Name;

/**
 * @implements \PHPStan\Rules\Rule<\PhpParser\Node\Expr\FuncCall>
 */
class NoEnvOutsideConfigDirRule implements \PHPStan\Rules\Rule
{

    private $configPath;
	public function __construct()
	{
        $this->configPath = getcwd() . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR;
	}

	public function getNodeType(): string
	{
		return \PhpParser\Node\Expr\FuncCall::class;
	}

	public function processNode(
		\PhpParser\Node $node,
		Scope $scope
	): array
	{
        $isEnvCall = $node->name instanceof Name && $node->name->getLast() === 'env';

        if (!$isEnvCall) {
            return [];
        }

        if (Str::startsWith($scope->getFile(), $this->configPath)) {
            return [];
        }

		return [
            'no env() outside config directory is allowed. Variables will not work when configuration is cached'
        ];
	}

}