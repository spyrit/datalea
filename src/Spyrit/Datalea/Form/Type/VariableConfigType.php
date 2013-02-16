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
                'label' => 'Name',
                'help' => 'Set the variable name to use in column value with the pattern <strong>%variable_name%</strong>',
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
                'label' => 'Method (See <a target="_blank" href="https://github.com/fzaninotto/Faker#formatters">Faker Formatters</a>)',
                'help' => 'Select the Faker Formatter method to generate a random value for this variable.<br/> Some methods can have arguments.',
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
            ))
            ->add('fakerMethodArg1', 'text', array(
                'label' => 'Argument 1',
                'help' => 'First argument for faker method if available (see method name).',
                'help_type' => 'popover',
                'help_popover_position' => 'right',
                'required' => false,
                'attr' => array(
                    'style' => 'width: 95%',
                ),
                'row_attr' => array(
                    'class' => 'argument first-arg',
                ),
            ))
            ->add('fakerMethodArg2', 'text', array(
                'label' => 'Argument 2',
                'help' => 'Second argument for faker method if available (see method name)',
                'help_type' => 'popover',
                'required' => false,
                'attr' => array(
                    'style' => 'width: 95%',
                ),
                'row_attr' => array(
                    'class' => 'argument second-arg',
                ),
            ))
            ->add('fakerMethodArg3', 'text', array(
                'label' => 'Argument 3',
                'help' => 'Third argument for faker method if available (see method name)',
                'help_type' => 'popover',
                'required' => false,
                'attr' => array(
                    'style' => 'width: 95%',
                ),
                'row_attr' => array(
                    'class' => 'argument third-arg',
                ),
            ))
            ->add('unique', 'checkbox', array(
                'required' => false,
                'label' => 'Unique',
                'help' => 'Set this variable to be unique through each generated item',
                'help_type' => 'popover',
                'help_popover_position' => 'top',
                'attr' => array(
                    'style' => 'width: 95%',
                ),
                'row_attr' => array(
                    'class' => 'unique',
                ),
            ))
            ;
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