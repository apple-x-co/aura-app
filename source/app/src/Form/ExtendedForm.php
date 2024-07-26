<?php

declare(strict_types=1);

namespace MyVendor\MyPackage\Form;

use Aura\Html\HelperLocator;
use Aura\Input\BuilderInterface;
use Aura\Input\FilterInterface;
use Aura\Input\Form;

use function array_merge;
use function is_array;

/** @psalm-suppress PropertyNotSetInConstructor */
class ExtendedForm extends Form
{
    public function __construct(
        BuilderInterface $builder,
        FilterInterface $filter,
        private readonly HelperLocator $helper,
    ) {
        parent::__construct($builder, $filter);
    }

    /** @param array<string, string|int> $attr */
    public function widget(string $name, array $attr = []): string
    {
        $array = $this->get($name);
        if (is_array($array)) {
            $array['attribs'] = array_merge($array['attribs'] ?? [], $attr);
        }

        return (string) $this->helper->input($array); // @phpstan-ignore-line
    }

    public function csrfTokenWidget(): string
    {
        return $this->widget(AntiCsrf::INPUT_NAME);
    }

    /**
     * @return list<string>
     *
     * @psalm-suppress MoreSpecificReturnType
     * @psalm-suppress LessSpecificReturnStatement
     */
    public function errorMessages(string|null $name): array
    {
        return $this->getMessages($name);
    }
}
