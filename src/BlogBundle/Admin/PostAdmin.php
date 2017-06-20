<?php

namespace BlogBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class PostAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('author');
        $formMapper->add('category');
        $formMapper->add('title');
        $formMapper->add('content');
        $formMapper->add('date');
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('author');
        $datagridMapper->add('category');
        $datagridMapper->add('title');
        $datagridMapper->add('content');
        $datagridMapper->add('date');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->add('author');
        $listMapper->add('category');
        $listMapper->add('title');
        $listMapper->add('content');
        $listMapper->add('date');
    }
}
