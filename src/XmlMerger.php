<?php
namespace Rikby\XmlMerge;

/**
 * Class of XML Merger
 *
 * @package Rikby\XmlMerge
 */
class Merger
{
    /**
     * Collection nodes
     *
     * @var array
     */
    protected $collectionNodes = array();

    /**
     * Xpath of a process node
     *
     * @var string
     */
    protected $rootNodeName = 'config';

    /**
     * Add name of nodes which should a collection
     *
     * It means there might be same nodes, in this case this node won't be overwritten.
     *
     * @param string $xpath
     * @return $this
     */
    public function addCollectionNode($xpath)
    {
        $this->collectionNodes[] = 'root/'.$xpath;

        return $this;
    }

    /**
     * Merge two XML
     *
     * @param \SimpleXMLElement|string $xmlSource
     * @param array|\SimpleXMLElement|string $xmlUpdates
     * @return \SimpleXMLElement
     */
    public function merge($xmlSource, $xmlUpdates)
    {
        if (is_string($xmlSource)) {
            $xmlSource = simplexml_load_string($xmlSource);
        }
        if (!is_array($xmlUpdates)) {
            $xmlUpdates = [$xmlUpdates];
        }
        foreach ($xmlUpdates as $xmlUpdate) {
            if (is_string($xmlUpdate)) {
                $xmlUpdate = simplexml_load_string($xmlUpdate);
            }
            $this->makeMerge($xmlSource, $xmlUpdate);
        }

        return $xmlSource;
    }

    /**
     * Append XML node (not overwrite)
     *
     * @param \SimpleXMLElement $to
     * @param \SimpleXMLElement $from
     * @return $this
     */
    public function xmlAppend(\SimpleXMLElement $to, \SimpleXMLElement $from)
    {
        $toDom   = dom_import_simplexml($to);
        $fromDom = dom_import_simplexml($from);
        $toDom->appendChild($toDom->ownerDocument->importNode($fromDom, true));

        return $this;
    }

    /**
     * Merge nodes
     *
     * @param \SimpleXMLElement $xmlSource
     * @param \SimpleXMLElement $xmlUpdate
     * @return $this
     */
    protected function makeMerge($xmlSource, $xmlUpdate)
    {
        /** @var \SimpleXMLElement $node */
        foreach ($xmlUpdate as $name => $node) {
            $this->addProcessXpathName($name);
            /** @var \SimpleXMLElement $nodeSource */
            $nodeSource = $xmlSource->$name;
            if ($this->isCollectionXpath() || !$nodeSource) {
                $this->xmlAppend($xmlSource, $node);
            } else {
                $this->mergeAttributes($nodeSource, $node);

                if ($node->count()) {
                    //merge child nodes
                    $this->makeMerge($nodeSource, $node);
                } else {
                    //set only value
                    $nodeSource[0] = (string) $node;
                }
            }
            $this->unsetProcessXpathName($name);
        }

        return $this;
    }

    /**
     * Add node to process xpath
     *
     * @param string $name
     * @return $this
     */
    protected function addProcessXpathName($name)
    {
        $this->rootNodeName .= '/'.$name;

        return $this;
    }

    /**
     * Check if such XPath means plenty nodes
     *
     * @return bool
     */
    protected function isCollectionXpath()
    {
        return in_array($this->rootNodeName, $this->collectionNodes);
    }

    /**
     * Merge attributes
     *
     * @param \SimpleXMLElement $xmlSource
     * @param \SimpleXMLElement $xmlUpdate
     * @return $this
     */
    protected function mergeAttributes($xmlSource, $xmlUpdate)
    {
        if (!$xmlSource->getName()) {
            return $this;
        }
        $attributes = (array) $xmlSource->attributes();
        $attributes = isset($attributes['@attributes']) ? $attributes['@attributes'] : array();
        foreach ($xmlUpdate->attributes() as $name => $value) {
            if (isset($attributes[$name])) {
                $xmlSource->attributes()->$name = (string) $value;
            } else {
                $xmlSource->addAttribute($name, (string) $value);
            }
        }

        return $this;
    }

    /**
     * Remove node name from process xpath
     *
     * @param string $name
     * @return $this
     */
    protected function unsetProcessXpathName($name)
    {
        $length             = strlen($this->rootNodeName);
        $lengthName         = strlen($name) + 1;
        $this->rootNodeName = substr($this->rootNodeName, 0, $length - $lengthName);

        return $this;
    }
}
