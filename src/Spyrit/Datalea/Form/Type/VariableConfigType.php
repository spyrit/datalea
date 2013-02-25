<?php

namespace Spyrit\Datalea\Form\Type;

use Spyrit\Datalea\Faker\Model\VariableConfig;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

/**
 * VariableConfigType
 *
 * @author Charles SANQUER - Spyrit Systemes <charles.sanquer@spyrit.net>
 */
class VariableConfigType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $methodsChoices = VariableConfig::getAvailableFakerMethods();

        $methods = array();
        foreach ($methodsChoices as $group => $methodsChoice) {
            $methods = array_merge($methods, array_keys($methodsChoice));
        }

        $builder
            ->add('name', 'text', array(
                'required' => true,
                'label' => 'generator.form.variables.name',
                'help' => 'generator.form.variables.name_help',
                'help_type' => 'popover',
                'help_popover_position' => 'right',
                'attr' => array(
                    'style' => 'width: 95%',
                ),
                'row_attr' => array(
                    'class' => 'name',
                ),
                'constraints' => array(
                    new Constraints\NotBlank(),
                    new Constraints\MinLength(3),
                )
            ))
            ->add('fakerMethod', 'choice', array(
                'required' => true,
                'label' => 'generator.form.variables.method',
                'help' => 'generator.form.variables.method_help',
                'help_type' => 'popover',
//                'help_popover_trigger' => 'click',
                'choices' => $methodsChoices,
                'attr' => array(
                    'class' => 'select2-box',
                    'style' => 'width: 95%',
                ),
                'row_attr' => array(
                    'class' => 'faker-method',
                ),
                'constraints' => array(
                    new Constraints\Choice(array(
                        'choices' => $methods,
                    )),
                )
            ));

            $arguments = array(
                'first',
                'second',
                'third',
            );

            for ($i = 0; $i < count($arguments); $i++) {
                $builder->add('fakerMethodArg'.($i+1), 'text', array(
                    'label' => 'generator.form.variables.argument'.($i+1),
                    'help' => 'generator.form.variables.argument'.($i+1).'_help',
                    'help_type' => 'popover',
                    'help_popover_position' => $i == 0 ? 'right' : 'top',
                    'required' => false,
                    'attr' => array(
                        'style' => 'width: 95%',
                    ),
                    'row_attr' => array(
                        'class' => 'argument '.$arguments[$i].'-arg',
                    ),
                ));
            }
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Spyrit\Datalea\Faker\Model\VariableConfig',
        ));
    }

    public function getName()
    {
        return 'datalea_generator_variable_config';
    }
}
