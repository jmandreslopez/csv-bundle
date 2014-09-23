<?php

namespace Sample\CsvBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AddFormType extends AbstractType
{
    /**
     * Build form
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', 'text', array(
                'label' => 'Type:',
                'required' => true,
            ))
            ->add('file', 'file', array(
                'label' => 'File:',
                'required' => true,
            ))
            ->add('name', 'hidden', array(
                'required' => false
            ));

        $builder->addEventListener(FormEvents::PRE_BIND, function (FormEvent $event) {
            $data = $event->getData();

            if (!$data || !array_key_exists('file', $data)) {
                return;
            }

            $data['filename'] = $data['file']->getFilename();
            $event->setData($data);
        });
    }

    /**
     * Set default options
     *
     * @param OptionsResolverInterface $resolver The resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'field_choices' => array()
        ));
    }

    /**
     * Get the forms name
     *
     * @return string name
     */
    public function getName()
    {
        return 'csv_add_type';
    }
}