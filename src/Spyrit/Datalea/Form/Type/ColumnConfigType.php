<?php

namespace Spyrit\Datalea\Form\Type;

use Spyrit\Datalea\Faker\Model\ColumnConfig;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

/**
 * ColumnConfigType
 *
 * @author Charles SANQUER - Spyrit Systemes <charles.sanquer@spyrit.net>
 */
class ColumnConfigType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $convertMethods = ColumnConfig::getAvailableConvertMethods();

        $builder
            ->add('name', 'text', array(
                'required' => true,
                'label' => 'generator.form.columns.name',
                'help' => 'generator.form.columns.name_help',
                'help_type' => 'popover',
                'help_popover_position' => 'right',
                'attr' => array(
//                    'style' => 'width: 95%',
                ),
                'row_attr' => array(
                    'class' => 'name',
                ),
                'constraints' => array(
                    new Constraints\NotBlank(),
                    new Constraints\Length(array('min' => 3)),
                )
            ))
            ->add('value', 'text', array(
                'required' => true,
                'label' => 'generator.form.columns.value',
                'help' => 'generator.form.columns.value_help',
                'help_type' => 'popover',
                'attr' => array(
//                    'style' => 'width: 95%',
                ),
                'row_attr' => array(
                    'class' => 'value',
                ),
                'constraints' => array(
                    new Constraints\NotBlank(),
                    new Constraints\Length(array('min' => 1)),
                )
            ))
            ->add('convertMethod', 'choice', array(
                'required' => false,
                'empty_value' => '',
                'label' => 'generator.form.columns.convert',
                'help' => 'generator.form.columns.convert_help',
                'help_type' => 'popover',
                'choices' => $convertMethods,
                'attr' => array(
                    'class' => 'select2-empty-box',
                    'style' => 'width: 100%',
                ),
                'row_attr' => array(
                    'class' => 'convert-method',
                ),
                'constraints' => array(
                    new Constraints\Choice(array(
                        'choices' => array_keys($convertMethods),
                    )),
                )
            ))
            ->add('unique', 'checkbox', array(
                'required' => false,
                'label' => 'generator.form.columns.unique',
                'help' => 'generator.form.columns.unique_help',
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
            'data_class' => 'Spyrit\Datalea\Faker\Model\ColumnConfig',
        ));
    }

    public function getName()
    {
        return 'datalea_generator_column_config';
    }
}
