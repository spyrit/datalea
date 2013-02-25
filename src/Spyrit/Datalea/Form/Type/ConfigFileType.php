<?php

namespace Spyrit\Datalea\Form\Type;

use \Symfony\Component\Form\AbstractType;
use \Symfony\Component\Form\FormBuilderInterface;
use \Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

/**
 * ConfigFileType
 *
 * @author Charles SANQUER - Spyrit Systemes <charles.sanquer@spyrit.net>
 */
class ConfigFileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('configFile', 'file', array(
                'required' => true,
                'label' => 'generator.form.configuration.file',
                'help' => 'generator.form.configuration.file_help',
                'help_type' => 'popover',
                'help_popover_position' => 'right',
                'constraints' => array(
                    new Constraints\File(array(
                        'maxSize' => '1024k',
                        'mimeTypes' => array(
                            'text/xml',
                            'application/xml',
                        ),
                        'mimeTypesMessage' => 'Please upload a valid XML config file',
                    ))
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
        ));
    }

    public function getName()
    {
        return 'datalea_generator_config_file';
    }
}
