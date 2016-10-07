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
            ->add('name')
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
                ]
            ])
            // META
            ->add(
                $builder->create('meta', FormType::class, array('by_reference' => 'meta'))
                // FUNCTION
                ->add(
                    $builder->create('function', FormType::class, array('by_reference' => 'function'))
                        ->add('function', TextareaType::class,[
                            'required'=> false,
                            'attr'=>[
                                'class'=> 'for-type for-function'
                            ]
                        ])
                )
                // JSON
                ->add(
                    $builder->create('json', FormType::class, array('by_reference' => 'json'))
                        ->add('url', UrlType::class,[
                            'required'=> false,
                            'attr'=>[
                                'class'=> 'for-type for-json for-api'
                            ]
                        ])
                        ->add('key', TextType::class,[
                            'required'=> false,
                            'attr'=>[
                                'class'=> 'for-type for-json for-api'
                            ]
                        ])
                        ->add('showkey', TextType::class,[
                            'required'=> false,
                            'attr'=>[
                                'class'=> 'for-type for-json for-api'
                            ]
                        ])
                )
                // ROUTE
                ->add(
                    $builder->create('route', FormType::class, array('by_reference' => 'route'))
                        ->add('route', RouteType::class,[
                            'attr'=>[
                                'class'=> 'for-type for-route'
                            ]
                        ])
                )
                // SERVICE
                /**
                * TODO : CREER le type
                **/
                ->add(
                    $builder->create('service', FormType::class, array('by_reference' => 'service'))
                        ->add('service', RouteType::class,[
                            'attr'=>[
                                'class'=> 'for-type for-route'
                            ]
                        ])
                )
                // DATA
                ->add(
                    $builder->create('DATA', FormType::class, array('by_reference' => 'DATA'))
                        ->add('DATA', ChoiceType::class,[
                            'choices'=>[],
                            'multiple'=>true,
                            'attr'=>[
                                'class'=> 'for-type for-data'
                            ]
                        ])
                        // folder
                ->add(
                    $builder->create('folder', FormType::class, array('by_reference' => 'folder'))
                        ->add('folder', ChoiceType::class,[
                            'choices'=>['dir_user'],
                            'attr'=>[
                                'class'=> 'for-type for-data'
                            ]
                        ])
                )
            ->add('submit', SubmitType::class, array(
                'label' => 'action.save',
                'attr' => array(
                    'class' => 'save btn btn-success m-c-t show-on-change',
                    'type'  => 'submit'
                ),
            ))
            )

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
