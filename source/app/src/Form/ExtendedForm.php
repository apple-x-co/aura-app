<?php

declare(strict_types=1);

namespace MyVendor\MyPackage\Form;

use Aura\Html\HelperLocator;
use Aura\Input\BuilderInterface;
use Aura\Input\FilterInterface;
use Aura\Input\Form;

use function array_merge;

class ExtendedForm extends Form
{
    public function __construct(
        BuilderInterface $builder,
        FilterInterface  $filter,
        private readonly HelperLocator $helper,
    )
    {
        parent::__construct($builder, $filter, []);
    }

    public function widget(string $name, array $attr = []): string
    {
        $array = $this->get($name);
        $array['attribs'] = array_merge($array['attribs'], $attr);

        return (string) $this->helper->input($array);
    }
}
