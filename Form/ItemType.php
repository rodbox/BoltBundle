<?php

namespace RB\BoltBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DatetimeType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use RB\CoreBundle\Form\Type\GenderType;
use RB\BoltBundle\Form\Type\RouteType;

class ItemType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class,[
                'required'=> true,
                'attr'=>[
                    'class'=>''
                ]
                ])
            ->add('description', TextareaType::class,[
                'required'=> false,
                ])
            ->add('context', ChoiceType::class,[
                'required'=> false,
                'choices'=>['default'=>'default']
                ])
            ->add('view', ChoiceType::class,[
                'required'=> false,
                'choices'=>['default'=>'default']
                ])
            ->add('multiple')
            ->add('type', EntityType::class, [
                'required'=> false,
                'class'=>'RBBoltBundle:Type',
                'choice_label' => 'name',
                'attr'         => [
                    'class'     => 'formFilterContent',
                    'data-cb'   => 'formFilterContent',
                    'data-cb-app'=> 'bolt'
                ]
            ]
        );
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'RB\BoltBundle\Entity\Item'
        ));
    }
}
