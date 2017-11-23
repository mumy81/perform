<?php

namespace Perform\BaseBundle\Exporter;

use Perform\BaseBundle\Config\ConfigStoreInterface;
use Perform\BaseBundle\Selector\EntitySelector;
use Perform\BaseBundle\Type\TypeRegistry;
use Exporter\Exporter as BaseExporter;
use Perform\BaseBundle\Admin\AdminRequest;
use Perform\BaseBundle\Config\TypeConfig;
use Perform\BaseBundle\Config\ExportConfig;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class Exporter
{
    protected $configStore;
    protected $selector;
    protected $typeRegistry;
    protected $writerFactory;

    public function __construct(ConfigStoreInterface $configStore, EntitySelector $selector, TypeRegistry $typeRegistry, WriterFactoryInterface $writerFactory)
    {
        $this->configStore = $configStore;
        $this->selector = $selector;
        $this->typeRegistry = $typeRegistry;
        $this->writerFactory = $writerFactory;
    }

    /**
     * @return StreamedResponse
     */
    public function getResponse(AdminRequest $adminRequest, $format)
    {
        $entity = $adminRequest->getEntity();
        $exportConfig = $this->configStore->getExportConfig($entity);
        $exporter = new BaseExporter($this->getWritersFromConfig($exportConfig));

        $query = $this->selector->getQueryBuilder($adminRequest, $entity)->getQuery();
        $exportFields = $this->configStore->getTypeConfig($entity)->getTypes(TypeConfig::CONTEXT_EXPORT);
        $source = new TypedDoctrineORMQuerySourceIterator($this->typeRegistry, $query, $exportFields);

        return $exporter->getResponse($format, $exportConfig->getFilename($format), $source);
    }

    protected function getWritersFromConfig(ExportConfig $exportConfig)
    {
        $writers = [];
        foreach ($exportConfig->getFormatOptions() as $format => $options) {
            $writers[$format] = $this->writerFactory->create($format, $options);
        }

        return $writers;
    }
}