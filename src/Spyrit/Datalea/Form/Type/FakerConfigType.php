<?php

namespace Spyrit\Datalea\Form\Type;

use \Spyrit\Datalea\Faker\Dump\Dumper;
use \Spyrit\Datalea\Faker\Model\Config;
use \Symfony\Component\Form\AbstractType;
use \Symfony\Component\Form\FormBuilderInterface;
use \Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

/**
 * FakerConfigType
 *
 * @author Charles SANQUER - Spyrit Systemes <charles.sanquer@spyrit.net>
 */
class FakerConfigType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $locales = Config::getAvailableFakerLocale();
        $formats = Dumper::getAvailableFormats();
        
        $builder
            ->add('className', 'text', array(
                'required' => true,
                'label' => 'Class or table name',
                'help' => 'set the item object class name or the database table name',
                'help_type' => 'popover',
                'help_popover_position' => 'right',
                'attr' => array(
                    'style' => 'width: 95%',
                ),
                'row_attr' => array(
                    'class' => '',
                ),
                'constraints' => array(
                    new Constraints\NotBlank(),
                    new Constraints\Length(array('min' => 3)),
                )
            ))
            ->add('locale', 'choice', array(
                'required' => true,
                'label' => 'Language',
                'choices' => $locales,
                'preferred_choices' => array('fr_FR', 'en_US', 'en_GB', ),
                'attr' => array(
                    'class' => 'select2-box',
                    'style' => 'width: 95%',
                ),
                'row_attr' => array(
                    'class' => '',
                ),
                'constraints' => array(
                    new Constraints\Choice(array(
                        'choices' => array_keys($locales),
                    )),
                )
            ))
            ->add('seed', 'integer', array(
                'required' => false,
                'label' => 'Seed',
                'help' => 'Set a seed to get always the same set of random values',
                'help_type' => 'popover',
                'help_popover_position' => 'right',
                'attr' => array(
                    'style' => 'width: 95%',
                ),
                'row_attr' => array(
                    'class' => '',
                ),
                'constraints' => array(
                    new Constraints\Type('integer'),
                    new Constraints\Range(array('min' => 0, 'max' => 50000)),
                )
            ))
            ->add('fakeNumber', 'integer', array(
                'required' => true,
                'label' => 'Number of rows',
                'help' => 'the number of rows with random columns to generate',
                'help_type' => 'popover',
                'attr' => array(
                    'style' => 'width: 95%',
                ),
                'row_attr' => array(
                    'class' => '',
                ),
                'constraints' => array(
                    new Constraints\Type('integer'),
                    new Constraints\Range(array('min' => 1, 'max' => 2000)),
                )
            ))
            ->add('variableConfigs', 'collection', array(
//                'required' => false,
                'label' => 'Variables',
                'type'   => new FakerVariableConfigType(),
                'prototype' => true,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'options'  => array(
//                    'required'  => false,
                ),
                'constraints' => array(
                    new Constraints\Count(array(
                        'min' => 1,
                        'max' => 100,
                        'minMessage' => 'You must set {{ limit }} variable(s) or more.',
                        'maxMessage' => 'You must set {{ limit }} variable(s) or less.',
                        'exactMessage' => 'You must set exactly {{ limit }} variable(s).',
                    )),
                )
                
            ))
            ->add('columnConfigs', 'collection', array(
//                'required' => false,
                'label' => 'Columns',
                'type'   => new FakerColumnConfigType(),
                'prototype' => true,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'options'  => array(
//                    'required'  => false,
                ),
                'constraints' => array(
                    new Constraints\Count(array(
                        'min' => 0,
                        'max' => 100,
                        'minMessage' => 'You must set {{ limit }} column(s) or more.',
                        'maxMessage' => 'You must set {{ limit }} column(s) or less.',
                        'exactMessage' => 'You must set exactly {{ limit }} column(s).',
                    )),
                )
                
            ))
            ->add('formats', 'choice', array(
                'required' => true,
                'multiple' => true,
                'expanded' => true,
                'label' => 'Output Formats',
                'help' => 'Select the output formats to dump',
                'help_type' => 'popover',
                'attr' => array(
                    'inline' => true,
                ),
                'choices' => $formats,
                'constraints' => array(
                    new Constraints\Count(array(
                        'min' => 1,
                        'max' => count($formats),
                        'minMessage' => 'You must set {{ limit }} format(s) or more.',
                        'maxMessage' => 'You must set {{ limit }} format(s) or less.',
                        'exactMessage' => 'You must set exactly {{ limit }} format(s).',
                    )),
                    new Constraints\Choice(array(
                        'choices' => array_keys($formats),
                        'multiple' => true,
                    )),
                )
            ))
            ->add('csvFormat', new CsvFormatType(), array(
                'label' => 'CSV options',
            ));
            ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Spyrit\Datalea\Faker\Model\Config',
            'cascade_validation' => true,
        ));
    }

    public function getName()
    {
        return 'datalea_generator_config';
    }
}
