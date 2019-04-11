<?php

namespace App\Form;

use App\Entity\Post;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('Title', null, ['required' => true])
            ->add('Content', TextareaType::class, ['required' => true])
            ->add('Category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => function ($cc) {
                    return $cc->getName();
                },
                'expanded' => false,
                'multiple' => false
            ])
            ->add('isDraft', CheckboxType::class, ['mapped' => false, 'required' => false])
            ->add('agreeTerms', CheckboxType::class, ['mapped' => false, 'required' => true]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id'   => 'post_type_csrf_string',
        ]);
    }
}
