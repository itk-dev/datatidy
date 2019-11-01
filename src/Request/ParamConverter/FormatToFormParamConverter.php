<?php


namespace App\Request\ParamConverter;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FormatToFormParamConverter implements ParamConverterInterface
{
    private $formFactory;

    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $format = $request->attributes->get('format');

        if (empty($format)) {
            throw new NotFoundHttpException();
        }

        $dataSourceClass = sprintf('App\Entity\%sDataSource', ucfirst($format));
        $dataSourceTypeClass = sprintf('App\Form\%sDataSourceType', ucfirst($format));

        if (!\class_exists($dataSourceClass) || !\class_exists($dataSourceTypeClass)) {
            throw new NotFoundHttpException();
        }

        $request->attributes->set(
            $configuration->getName(),
            $this->formFactory->create($dataSourceTypeClass, new $dataSourceClass)
        );

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(ParamConverter $configuration)
    {
        return $configuration->getConverter() === 'format_to_form';
    }
}
