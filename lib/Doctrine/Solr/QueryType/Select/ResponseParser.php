<?php
namespace Doctrine\Solr\QueryType\Select;
use Solarium\QueryType\Select\ResponseParser\ResponseParser as SelectResponseParser;

class ResponseParser extends SelectResponseParser
{
    /**
     * @var callable ($document)
     * @return converted $document
     */
    private $convert;

    /**
     * Creates ResponseParser capable of converting result documents.
     *
     * @param callable $convert
     */
    public function __construct(callable $convert) {
        $this->convert = $convert;
    }

    public function parse($result) {
        $out = parent::parse($result);

        // convert the documents
        $documents = [];
        if (isset($out['documents'])) {
            foreach ($out as $solrDocument) {
                $documents[] = call_user_func([$this->convert], $solrDocument);
            }
        }
        $out['documents'] = $documents;

        return $out;
    }
}
