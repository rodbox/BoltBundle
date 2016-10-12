<?php

namespace RB\BoltBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DatetimeType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class ProjectsType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->bolt = $options['bolt'];
        $this->roles = $this->bolt->roles();

        $builder
            ->add('name')
            ->add('description',TextareaType::class,[
                'attr'=>[
                    'rows'=>5
                ]
            ])
            ->add('security',ChoiceType::class,[
                'required'=>false,
                'multiple'=>true,
                'expanded'=>true,
                'choices'=>$this->roles
            ])
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('bolt');
        $resolver->setDefaults(array(
            'data_class' => 'RB\BoltBundle\Entity\Projects'
        ));
    }
}
