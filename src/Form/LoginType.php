<?php
/**
 * Class LoginType
 *
 * PHP Version 7.2.1
 *
 * @category Form
 *
 * @author   Adfab <dev@adfab.fr>
 *
 * @license  All right reserved
 *
 * @link     Null
 */
namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LoginType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Enter your email'
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Enter your password'
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Login!'
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
