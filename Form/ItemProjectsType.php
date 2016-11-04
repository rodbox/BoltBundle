<?php

namespace RB\BoltBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\GroupType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DatetimeType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use RB\CoreBundle\Form\Type\GenderType;
use RB\BoltBundle\Form\Type\RouteType;

class ItemProjectsType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $this->bolt     = $options['bolt'];
        
        $this->routes   = $this->bolt->routes();
        $this->services = $this->bolt->services();
        $this->init     = $this->bolt->init();
        $this->folder   = $this->bolt->folder();
        $this->entitys  = $this->bolt->entitys();
        $this->projects = $this->bolt->projects();
        $this->forms    = $this->bolt->forms();


        $builder
         // base
            ->add(
                $builder->create('base', FormType::class, array(
                    'by_reference' => 'base',
                    'attr'         => [
                        'class'=>''
                    ]
                    ))
                ->add('name', TextType::class,[
                    'required'=> true,
                    'attr'=>[
                        'class'=>'form-control setter-item-name'
                    ]
                    ])
                ->add('id', HiddenType::class,[
                    'required'=> false,
                    'attr'=>[]
                    ])
                ->add('type', EntityType::class, [

                    'class'=>'RBBoltBundle:Type',
                    'choice_label' => 'name',
                    'empty_data' => '1',
                    'attr'         => [
                        'class'     => 'formFilterContent form-control chosen',
                        'data-cb'   => 'formFilterContent',
                        'data-cb-app'=> 'bolt'
                    ]
                ])
                ->add('description', TextareaType::class,[
                    'required'=> false,
                    'attr' => [
                        'rows'=>'7',
                        'class'=>'form-control',
                        'data-editor'=>'text',
                        'data-helper'=>'La déscription de la configuration de l\'item',
                        ]
                    ])
                ->add('context', ChoiceType::class,[
                    'required'=> false,
                    'choices'=>['default'=>'default'],
                    'attr'=>[
                        'class'=>'form-control'
                    ]
                    ])
                ->add('view', ChoiceType::class,[
                    'required'=> false,
                    'choices'=>[
                        'default'=>'default',
                        'users'=>'users',
                        'colors'=>'colors',
                        'fa'=>'fa'
                    ],
                    'attr'=>[
                        'class'=>'form-control'
                    ]
                    ])
                ->add('multiple', CheckboxType::class,[
                    'required'=>false
                    ])
                ->add('lockme', CheckboxType::class,[
                    'required'=>false
                    ])
                )
            // ALL
            ->add(
                $builder->create('all', FormType::class, array(
                    'by_reference' => 'all',
                    'attr'         => [
                        'class'    =>''
                    ]
                    ))
                // hook
                        ->add('hook', NumberType::class,[
                            'required'=> false,
                            'attr'=>[
                                'type'=>'number',
                                'min' => 0,
                                'max' => 5,
                                 'class'=>'form-control'
                            ]
                        ])
                        ->add('default', TextType::class,[
                            'required'=> false,
                            'attr'=>[
                                 'class'=>'form-control'
                            ]
                        ])
                        ->add('automatic',  CheckboxType::class,[
                            'required'=>false
                        ])
            )
            // ALL
            // META
            ->add(
                $builder->create('meta', FormType::class, array(
                    'by_reference' => 'meta',
                    'attr'         => [
                        'class'=>''
                    ]
                    ))
                // FUNCTION
                ->add(
                    $builder->create('function', FormType::class, ["inherit_data" => true])
                        ->add('function_js', TextareaType::class,[
                            'required'=> false,
                            'attr'=>[
                                'class'=> 'for-type for-function form-control modal-edit cm-editor',
                                'data-editor'=>'codemirror',
                                'data-cm-mode'=>'javascript',
                                'rows'=>'7'
                            ]
                        ])
                        ->add('function_php', TextareaType::class,[
                            'required'=> false,
                            'attr'=>[
                                'class'=> 'for-type for-function form-control modal-edit cm-editor',
                                'data-editor'=>'codemirror',
                                'data-cm-mode'=>'php',
                                'rows'=>'7'
                            ]
                        ])
                        ->add('loop', CheckboxType::class,[
                            'required'=> false
                        ])
                        ->add('pointer', TextType::class,[
                            'required'=> false
                        ])
                )
                // JSON
                ->add(
                    $builder->create('json', FormType::class, array(
                        'by_reference' => 'json',
                        'attr'         => [
                            'class'=>''
                        ]
                        ))
                        ->add('url', UrlType::class,[
                            'required'=> false,
                            'attr'=>[
                                'class'=> 'for-type for-json for-api form-control'
                            ]
                        ])
                        ->add('dir', TextType::class,[
                            'required'=> false,
                            'attr'=>[
                                'class'=> 'for-type for-json for-api form-control'
                            ]
                        ])
                        ->add('key', TextType::class,[
                            'required'=> false,
                            'attr'=>[
                                'class'=> 'for-type for-json for-api form-control'
                            ]
                        ])
                        ->add('showkey', TextType::class,[
                            'required'=> false,
                            'attr'=>[
                                'class'=> 'for-type for-json for-api form-control'
                            ]
                        ])
                )
                // END: JSON
                // ROUTE
                ->add(
                    $builder->create('route', FormType::class, array(
                        'by_reference' => 'route',
                        'attr'         => [
                            'class'=>''
                        ]
                        ))
                        ->add('route', RouteType::class,[
                            'required'=> false,
                            'choices'=>$this->routes,
                            'attr'=>[
                                'class'=> 'for-type for-route form-control'
                            ]
                        ])
                )
                // END: ROUTE
                // entity
                ->add(
                    $builder->create('entity', FormType::class, array(
                        'required'=> false,
                        'by_reference' => 'entity',
                        'attr'         => [
                            
                        ]
                        ))
                        ->add('entity', ChoiceType::class,[
                            'required'=> false,
                            'choices'=>$this->entitys,
                            'attr'=>[
                                'class'=> 'for-type for-entity form-control set-data-entity'
                            ]
                        ])
                        ->add('index_value', ChoiceType::class,[
                            'required'=> false,
                            'choices'=>$this->entitys,
                            'attr'=>[
                                'class'=> 'for-type for-entity  form-control'
                            ]
                        ])
                        ->add('query', TextareaType::class,[
                            'required'=> false,
                            'attr'=>[
                                'class'=> 'for-type for-entity  form-control modal-edit',
                                'data-editor'=>'codemirror',
                                'data-cm-mode'=>'php',
                                'rows'=>'7'
                            ]
                        ])
                )
                // END: entity
                
                // SERVICE
                ->add(
                    $builder->create('service', FormType::class, array(
                        'required'=> false,
                        'by_reference' => 'service',
                        'attr'         => [
                            'class'=>''
                        ]
                        ))
                        ->add('service', ChoiceType::class,[
                            'required'=> false,
                            'choices'=> $this->services,
                            'attr'=>[
                                'class'=> 'for-service for-route form-control chosen'
                            ]
                        ])
                        ->add('service_action', TextareaType::class,[
                            'required'=> false,
                            'attr'=>[
                                'class'=> 'for-service for-service form-control modal-edit cm-editor',
                                'data-editor'=>'codemirror',
                                'data-cm-mode'=>'php',
                            ]
                        ])
                )
                // END : SERVICE
                // // forms
                ->add(
                    $builder->create('form', FormType::class, array(
                        'required'=> false,
                        'by_reference' => 'form',
                        'attr'         => [
                            'class'=>''
                        ]
                        ))
                        ->add('routes', ChoiceType::class,[
                            'required'=> false,
                            'choices'=>$this->routes,
                            'attr'=>[
                                'class'=> 'for-type for-route form-control '
                            ]
                        ])
                        ->add('form', TextType::class,[
                            'required'=> false,
   
                            'attr'=>[
                                'class'=> 'for-type for-route form-control '
                            ]
                        ])
                )
                // END : forms
                // FOLDER
                ->add(
                    $builder->create('folder', FormType::class, array(
                        'required'=> false,
                        'by_reference' => 'folder',
                        'attr'         => [
                            'class'=>''
                        ]
                        ))
                        ->add('folder', ChoiceType::class,[
                            'required'=> false,
                            'choices'=> $this->folder,
                            'attr'=>[
                                'class'=> 'for-type for-folder form-control'
                            ]
                        ])
                        ->add('type', ChoiceType::class,[

                            'choices'=> ['files'=>'files','directories'=>'directories'],
                            'attr'=>[
                                'class'=> 'for-type for-folder form-control'
                            ]
                        ])
                )
                // END : FOLDER
                // DATA
                ->add(
                    $builder->create('data', FormType::class, array(
                        'required'=> false,
                        'by_reference' => 'data',
                        'attr'         => [
                            'class'=>''
                        ]
                        ))
                        ->add('filter-content', TextareaType::class,[
                            'required'=> false,
                            'attr'=>[
                                'rows'=>5,
                                'class'=>'form-filter  form-control data-filter-content'
                            ]
                        ])
                        ->add('filter-action', ChoiceType::class,[
                            'required'=> false,
                            'choices'=>['url'=>'url','mail'=>'mail','line'=>'line'],
                            'attr'=>[
                                'class'=> 'for-type for-data form-control'
                            ]
                        ])
                        ->add('data', ChoiceType::class,[
                            'required'=> false,
                            'choices'=>[],
                            'multiple'=>true,
                            'attr'=>[
                                'class'=> 'for-type for-data form-control'
                            ]
                        ])
                )
                // END DATA
                // free
                ->add(
                    $builder->create('free', FormType::class, array(
                        'required'=> false,
                        'by_reference' => 'free',
                        'attr'         => [
                            'class'=>''
                        ]
                        ))
                        ->add('reg', TextType::class,[
                            'required'=> false,
                            'attr'=>[
                                'class'=>' form-control'
                            ]
                        ])
                       
                )
                // END free
                // init
                ->add(
                    $builder->create('init', FormType::class, array(
                        'required'=> false,
                        'by_reference' => 'init',
                        'attr'         => [
                            'class'=>''
                        ]
                        ))
                        ->add('vendor', ChoiceType::class,[
                            'choices'=>$this->init,
                            'multiple'=>false,
                            'expanded'=>false,
                            'required'=> false,
                            'attr'=>[
                                'class'=>'data-set-initvendor'
                            ]
                        ])
                        ->add('init', ChoiceType::class,[
                            'choices'=>['default'=>'default'],
                            'multiple'=>false,
                            'expanded'=>false,
                            'required'=> true,
                            'attr'=>[
                                'class'=>'data-get-initvendor'
                            ]
                        ])
                       
                )
                // END init
                // projects
                ->add(
                    $builder->create('bolt_project', FormType::class, array(
                        'required'=> false,
                        'by_reference' => 'Project',
                        'attr'         => [
                            'class'=>''
                        ]
                        ))
                        ->add('projects', ChoiceType::class,[
                            'choices'=>$this->projects,
                            'multiple'=>true,
                            'expanded'=>true,
                            'required'=> false,
                            'attr'=>[
                                'class'=>''
                            ]
                        ])
                       
                )
                // END projects

                )
                ->add(
                $builder->create('submit', FormType::class, array(
                    'by_reference' => 'action',
                    'attr'         => [
                        'class'=>''
                    ]
                    ))
                ->add('submit', SubmitType::class, array(
                    'label' => 'action.save',
                    'attr' => array(
                        'class' => 'save btn btn-success m-c-t show-on-change',
                        'type'  => 'submit'
                    )
                ))
            );
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {

        $resolver->setRequired('bolt');
        $resolver->setDefaults(array(
            'data_class' => 'RB\BoltBundle\Entity\Item'
        ));
    }
}
