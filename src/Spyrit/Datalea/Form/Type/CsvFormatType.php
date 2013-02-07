<?php

namespace Spyrit\Datalea\Form\Type;

use Spyrit\Datalea\Faker\Model\CsvFormat;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

/**
 * CsvFormatType
 *
 * @author Charles SANQUER - Spyrit Systemes <charles.sanquer@spyrit.net>
 */
class CsvFormatType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $eols = CsvFormat::getAvailableEols();
        $encodings = CsvFormat::getAvailableEncodings();
        
        $builder
            ->add('delimiter', 'text', array(
                'required' => true,
                'label' => 'delimiter',
//                'help' => '',
//                'help_type' => 'popover',
//                'help_popover_position' => 'right',
                'attr' => array(
                    'class' => 'input-mini',
                ),
                'row_attr' => array(
                    'class' => 'control-group span2',
                ),
                'constraints' => array(
                    new Constraints\NotBlank(),
                    new Constraints\Length(array('min' => 1, 'max' => 1)),
                )
            ))
            ->add('enclosure', 'text', array(
                'required' => true,
                'label' => 'enclosure',
//                'help' => '',
//                'help_type' => 'popover',
//                'help_popover_position' => 'top',
                'attr' => array(
                    'class' => 'input-mini',
                ),
                'row_attr' => array(
                    'class' => 'control-group span2',
                ),
                'constraints' => array(
                    new Constraints\NotBlank(),
                    new Constraints\Length(array('min' => 1, 'max' => 1)),
                )
            ))
            ->add('escape', 'text', array(
                'required' => true,
                'label' => 'escape',
//                'help' => '',
//                'help_type' => 'popover',
//                'help_popover_position' => 'top',
                'attr' => array(
                    'class' => 'input-mini',
                ),
                'row_attr' => array(
                    'class' => 'control-group span3',
                ),
                'constraints' => array(
                    new Constraints\NotBlank(),
                    new Constraints\Length(array('min' => 1, 'max' => 1)),
                )
            ))
            ->add('eol', 'choice', array(
                'required' => true,
                'label' => 'end of line',
//                'help' => '',
//                'help_type' => 'popover',
                'choices' => $eols,
                'attr' => array(
                    'class' => 'select2-box',
                    'style' => 'width: 95%',
                ),
                'row_attr' => array(
                    'class' => 'control-group span2',
                ),
                'constraints' => array(
                    new Constraints\Choice(array(
                        'choices' => array_keys($eols),
                    )),
                )
            ))
            ->add('encoding', 'choice', array(
                'required' => true,
                'label' => 'encoding',
//                'help' => '',
//                'help_type' => 'popover',
                'choices' => $encodings,
                'attr' => array(
                    'class' => 'select2-box',
                    'style' => 'width: 95%',
                ),
                'row_attr' => array(
                    'class' => 'control-group span3',
                ),
                'constraints' => array(
                    new Constraints\Choice(array(
                        'choices' => array_keys($encodings),
                    )),
                )
            ))
            ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Spyrit\Datalea\Faker\Model\CsvFormat',
        ));
    }
    
    public function getName()
    {
        return 'datalea_generator_csv_format';
    }
}