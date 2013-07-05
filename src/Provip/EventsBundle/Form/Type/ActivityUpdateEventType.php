<?php

namespace Provip\EventsBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Provip\UserBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ActivityUpdateEventType extends AbstractType
{

    protected $student;

    public function __construct(User $student)
    {
        $this->student = $student;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('activity', 'entity', array(
                    'label' => 'Activity',
                    'error_bubbling' => true,
                    'attr' => array('class' => 'selectpicker', 'data-style' => 'btn-info'),
                    'class' => 'ProvipProvipBundle:Activity',
                    'query_builder' => function(EntityRepository $er) {

                        $student = $this->student;

                        return $er->createQueryBuilder('a')
                            ->join('a.application', 'ap')
                            ->where('a.student = ?1')
                            ->andwhere('ap.approvedByHei = ?2')
                            ->andwhere('ap.approvedByCompany = ?3')
                            ->andwhere('a.state != ?4')
                            ->orderBy('a.deadline', 'ASC')
                            ->setParameters(array('1' => $student, '2' => true, '3' => true, '4' => 'Completed'))
                            ;
                    },)
            )
            ->add('message', 'textarea', array(
                'label' => 'Message',
                'error_bubbling' => true,
            ))
            ->add('privacy', 'choice', array(
                'choices' => array(
                    'privacy.hei.only' => 'Share with HEI only',
                    'privacy.company.only' => 'Share with Company only',
                    'privacy.internship'   => 'Share with HEI and Company'
                ),
                'label' => 'Privacy Settings',
                'attr' => array('class' => 'selectpicker'),
                'error_bubbling' => true,
            ))
            ->add('activityState', 'choice', array(
                'choices' => array(
                    'Not yet started' => 'Not yet started',
                    'Just started' => 'Just started',
                    'Almost there'   => 'Almost there',
                    'Completed'      => 'Completed'
                ),
                'label' => 'Progress',
                'error_bubbling' => true,
                'attr' => array('class' => 'selectpicker', 'data-style' => 'btn-info')
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Provip\EventsBundle\Entity\ActivityUpdateEvent'
        ));
    }

    public function getName()
    {
        return 'provip_eventsbundle_activityupdateeventtype';
    }
}