<?php
namespace Rikby\MergeXml\Test\Merger;
use Rikby\MergeXml\Merger;

/**
 * Class MergerTest
 */
class MergerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test instance of
     */
    public function testInstanceOf()
    {
        $this->assertInstanceOf('Rikby\MergeXml\Merger', $this->getMock('Rikby\MergeXml\Merger', array(), array(), '', false));
    }

    /**
     * Test merge two XML contents
     */
    public function testMerge()
    {
        $xml1 = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<some_name>
    <section attr10="val1" attr20="val2">
        <collection_node>
            <child_node1 />
        </collection_node>
    </section>
    <value_node>12</value_node>
    <unpair_tag/>
</some_name>
XML;
        $xml2 = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<some_name>
    <section attr2="val2" attr20="val22">
        <collection_node>
            <child_node2 />
        </collection_node>
        <collection_node>
            <child_node3 />
        </collection_node>
    </section>
    <another_section>
        <collection_node>
            <some_child>
                <foo>foo</foo>
            </some_child>
        </collection_node>
        <collection_node>
            <another_child>
                <foo>bar</foo>
            </another_child>
        </collection_node>
    </another_section>
    <value_node>44</value_node>
</some_name>
XML;
        $expected = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<some_name>
    <section attr10="val1" attr20="val22" attr2="val2">
        <collection_node>
            <child_node1/>
        </collection_node>
        <collection_node>
            <child_node2/>
        <collection_node>
        </collection_node>
            <child_node3/>
        </collection_node>
    </section>
    <value_node>44</value_node>
    <unpair_tag/>
    <another_section>
        <collection_node>
            <some_child>
                <foo>foo</foo>
            </some_child>
        <collection_node>
        </collection_node>
            <another_child>
                <foo>bar</foo>
            </another_child>
        </collection_node>
    </another_section>
</some_name>

XML;
        $test = new Merger();
        $test->addCollectionNode('section/collection_node');
        $test->addCollectionNode('another_section/collection_node');
        $simpleXml = $test->merge($xml1, $xml2);

        //reformat results
        $expected = preg_replace('/\n\s+/', "\n", $expected);
        $actual = preg_replace('/\n\s+/', "\n", $simpleXml->asXML());
        $actual = preg_replace('/></', ">\n<", $actual);

        //test
        $this->assertInstanceOf('SimpleXMLElement', $simpleXml);
        $this->assertEquals($expected, $actual);
    }
}
