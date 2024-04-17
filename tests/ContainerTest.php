<?php

declare(strict_types=1);

namespace Baueri\Spire\Framework\Tests;

use Baueri\Spire\Framework\Application;
use Baueri\Spire\Framework\Container;
use Baueri\Spire\Framework\Http\Request;
use Baueri\Spire\Framework\Http\Route\RouterInterface;
use Baueri\Spire\Framework\Http\Route\XmlRouter;
use Baueri\Spire\Framework\Support\Config;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use stdClass;

class ContainerTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();

        Container::reset();
    }

    public function testSingleton(): void
    {
        $container = Container::getInstance();
        $container->singleton('test', fn() => new stdClass());

        $this->assertSame($container->get('test'), $container->get('test'));

    }

    public function testSingletonArray(): void
    {
        $container = Container::getInstance();

        $container->singleton([
            'test1' => fn() => new stdClass(),
            'test2' => fn() => new stdClass(),
        ]);

        $this->assertSame($container->get('test1'), $container->get('test1'));
        $this->assertSame($container->get('test2'), $container->get('test2'));
    }

    public function testContainerThrowsExceptionIfSingletonClassIsRegisteredTwice(): void
    {
        $container = Container::getInstance();
        $container->singleton('test', fn() => new stdClass());

        $this->expectException(RuntimeException::class);
        $container->singleton('test', fn() => new stdClass());
    }

    public function testGetUnboundClass(): void
    {
        $container = Container::getInstance();

        $item = $container->get(stdClass::class);

        $this->assertIsObject($item);
    }

    public function testBind(): void
    {
        $container = Container::getInstance();
        $container->bind('test', fn() => new stdClass());

        $this->assertIsObject($container->get('test'));
        $this->assertNotSame($container->get('test'), $container->get('test'));
    }

    public function testBindingClassString(): void
    {
        $container = Container::getInstance();
        $container->bind(stdClass::class);

        $this->assertIsObject($container->get(stdClass::class));
    }

    public function testBindThrowsExceptionWhenNoAbstractionIsGiven(): void
    {
        $container = Container::getInstance();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('abstraction name must not be empty');
        $container->bind('', fn() => new stdClass());
    }

    public function testBindThrowsExceptionWhenBindingIsRegisteredTwice(): void
    {
        $container = Container::getInstance();
        $container->bind('test', fn() => new stdClass());

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('test already has a binding');
        $container->bind('test', fn() => new stdClass());
    }

    public function testArgumentsPassedToBindingClosure(): void
    {
        $container = Container::getInstance();
        $container->bind('test', fn($name) => $name);

        $this->assertSame('test', $container->get('test', ['name' => 'test']));
    }

    public function testResolvedDependenciesArePassedToBindingClosure(): void
    {
        $container = Container::getInstance();
        $container->bind('container', fn(Container $container) => $container);

        $this->assertInstanceOf(Container::class, $container->get('container'));
    }

    public function testResolvedSingleDependencyIsInstancedOnlyOnceWhenPassingToBindingClosure(): void
    {
        $container = Container::getInstance();
        $container->singleton(stdClass::class, fn() => new stdClass());

        $container->bind('test', fn (stdClass $stdClass) => $stdClass);

        $this->assertSame($container->get(stdClass::class), $container->get('test'));
    }

    public function testBindingMultipleAbstractions(): void
    {
        $container = Container::getInstance();
        $container->bind([
            'test1' => fn () => 'test one',
            'test2' => fn () => 'test two',
        ]);

        $this->assertSame($container->get('test1'), 'test one');
        $this->assertSame($container->get('test2'), 'test two');
    }

    public function testOverridingAlreadyBoundItem(): void
    {
        $container = Container::getInstance();
        $test1 = new stdClass();
        $test1->name = '1';

        $test2 = new stdClass();
        $test2->name = '2';

        $container->bind('test', fn () => $test1);
        $container->bind('test', fn () => $test2, true);

        $this->assertSame($test2, $container->get('test'));
    }

    public function testHas(): void
    {
        $container = Container::getInstance();
        $container->singleton('test', fn() => new stdClass());

        $container->get('test');

        $this->assertTrue($container->has('test'));
        $this->assertFalse($container->has('test2'));
    }

    public function testMake1(): void
    {
        $container = Container::getInstance();
        $container->singleton('test', fn () => new stdClass());

        $item1 = $container->make('test');

        $this->assertIsObject($item1);
    }

    public function testMakeThrowsErrorWithUnboundInterface(): void
    {
        $container = Container::getInstance();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Class `ThisIsDefinitelyNotAClass` does not exist');
        $container->make('ThisIsDefinitelyNotAClass');
    }

    public function testMakeThrowsExceptionWhenGivenAbstractionIsNotBoundNorAnInstantiableClass(): void
    {
        $container = Container::getInstance();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot instantiate interface Baueri\Spire\Framework\Tests\StubInterface without a binding registered to it');
        $container->make(StubInterface::class);
    }

    public function testClassDependencies(): void
    {
        $container = Container::getInstance();

        $stub1Dependencies = $container->getDependencies(DependencyTestStub::class);

        $this->assertEquals([
            new StubA(),
            new StubB(),
        ], $stub1Dependencies);

        $stub2MethodDependencies = $container->getDependencies(StubA::class, 'doSomething');
        $this->assertEquals([
            new StubB(),
        ], $stub2MethodDependencies);
    }

    public function testGetDependenciesThrowsExceptionWhenMethodDoesNotExist(): void
    {
        $container = Container::getInstance();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Method Baueri\Spire\Framework\Tests\StubA::doSomethingThatDoesNotExist does not exist');
        $container->getDependencies(StubA::class, 'doSomethingThatDoesNotExist');
    }

    public function testGetDependenciesReturnsEmptyArrayWhenBindingIsASimpleString(): void
    {
        $container = Container::getInstance();

        $dependencies = $container->getDependencies('test');

        $this->assertEmpty($dependencies);
    }

    public function testGetClassWithConstructorDependencies(): void
    {
        $container = Container::getInstance();
        $instance = $container->get(DependencyTestStub::class);

        $this->assertInstanceOf(StubA::class, $instance->stubA);
        $this->assertInstanceOf(StubB::class, $instance->stubB);

        $stubB = $container->get(StubB::class);

        $this->assertEquals('test', $stubB->name);
    }

    public function testClosureDependencies(): void
    {
        $container = Container::getInstance();

        $function = fn (StubA $a) => $a;

        $dependencies = $container->getDependencies($function);

        $this->assertEquals([
            new StubA(),
        ], $dependencies);
    }

    public function testGetDependenciesWithDefaultValue(): void
    {
        $container = Container::getInstance();

        $function = fn (StubA $a, $b = 'test') => $b;

        $dependencies = $container->getDependencies($function);

        $this->assertEquals([new StubA(), 'test'], $dependencies);
    }

    public function testGetDependenciesWithAlreadyPassedDependencies(): void
    {
        $container = Container::getInstance();

        $function = fn (StubA $a, $b = 'test') => $b;

        $stubA = new StubA('test2');
        $dependencies = $container->getDependencies($function, resolvedDependencies: [$stubA, 'test3']);

        $this->assertEquals('test2', $dependencies[0]->name);
        $this->assertEquals('test3', $dependencies[1]);
    }

    public function testGetEnumDependencyUsingRequest(): void
    {
        $container = Application::create(__DIR__ . '/../');
        $container->singleton(RouterInterface::class, XmlRouter::class);
        $container->singleton(Request::class);
        $container->bind(Config::class, function () {
            return new Config('tests/Support/Fixtures/');
        });

        request()->set('stubEnum', 'one');

        $function = fn (StubEnumA $stubEnum) => $stubEnum;

        $dependencies = $container->getDependencies($function);

        $this->assertEquals(StubEnumA::one, $dependencies[0]);
    }

    /**
     * ??? Biztos van ennek ertelme?
     */
    public function testGetBindingOfParentClass(): void
    {
        $container = Container::getInstance();

        $container->bind(StubA::class, StubA::class);

        $binding = $container->getBinding(StubB::class);

        $this->assertEquals(StubA::class, $binding);
    }

    public function testShare(): void
    {
        $container = Container::getInstance();
        $container->share('test', fn() => new stdClass());

        $this->assertSame($container->get('test'), $container->get('test'));
    }
}

interface StubInterface
{

}

class StubA
{
    public function __construct(
        public readonly string $name = 'test'
    ) {
    }

    public function doSomething(StubB $stubB) {

    }
}

class StubB extends StubA
{

}

class DependencyTestStub
{
    public function __construct(
        public StubA $stubA, public StubB $stubB
    ) {
    }
}


enum StubEnumA
{
    case one;
    case two;
}
