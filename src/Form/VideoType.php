<?php


namespace App\Form;


use App\Entity\Video;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VideoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('url', TextType::class,[
                'label' => 'URL Video',
                'attr' => [
                    'class' => 'form-control'
                ],
                'label_attr' => [
                    'class' => 'bmd-label-floating'
                ],
                'required' => true
            ])
            ->add('urlImage', TextType::class,[
                'label' => 'URL Image',
                'attr' => [
                    'class' => 'form-control'
                ],
                'label_attr' => [
                    'class' => 'bmd-label-floating'
                ],
                'required' => true
            ])
            ->add('title', TextType::class,[
                'label' => 'Title',
                'attr' => [
                    'class' => 'form-control'
                ],
                'label_attr' => [
                    'class' => 'bmd-label-floating'
                ],
                'required' => true
            ])
            ->add('description', TextType::class,[
                'label' => 'Description',
                'attr' => [
                    'class' => 'form-control'
                ],
                'label_attr' => [
                    'class' => 'bmd-label-floating'
                ],
                'required' => true
            ])
            ->add('save', SubmitType::class,[
                'label' => 'Guardar',
                'attr' => [
                    'class' => 'btn btn-success'
                ]
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Video::class
        ]);
    }

}