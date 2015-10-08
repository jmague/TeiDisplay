<?php




class TeiDisplayPlugin extends Omeka_Plugin_AbstractPlugin
{

    protected $_hooks = array('install', 'uninstall','initialize','public_head');
    
    public function hookInstall($args)
    {
       if (!class_exists('XSLTProcessor')) {
        throw new Exception('Unable to access XSLTProcessor class.  Make sure the php-xsl package is installed.');
    } else{
        $xh = new XSLTProcessor; 
    }

    //Omeka_View_Helper_FileMarkup::addMimeTypes(array('application/xml', 'text/xml'),TeiDisplay);

    }

    public function hookUninstall($args)
    {
        
    }

    public function hookInitialize($args)
    {
        Omeka_View_Helper_FileMarkup::addMimeTypes(array('application/xml', 'text/xml'),'TeiDisplayPlugin::TeiDisplay');        
        Omeka_View_Helper_FileMarkup::addMimeTypes('text/x-c','TeiDisplayPlugin::CssDisplay');        
    }

    public function CssDisplay($file, array $options=array())
    {
    }

    public function TeiDisplay($file, array $options=array())
    {
        if ($file->getExtension() != "xml")
            return "";
        
        //queue_css_file('tei_display_public', 'screen', false, "plugins/TeiDisplay/views/public/css");

        //echo "<h3>displaying ", $file->original_filename, "</h3><br/>";
        $files = $file->getItem()->Files;
        foreach ($files as $f) {
            if ($f->getExtension()=="xsl")
                $xsl_file=$f;
            if ($f->getExtension()=="css")
                $css_file=$f;
        }
        //queue_css_url($css_file->getWebPath());
        echo '<link rel="stylesheet" media="screen" href="' . $css_file->getWebPath().'"/>';
        //echo "transforming with ", $xsl_file->original_filename, "<br/>";
        
        $xp = new XsltProcessor();
        $xsl = new DomDocument;
        //echo "loading ", "files/original/".$xsl_file->filename, "<br/>";
        $xsl->load("files/original/".$xsl_file->filename);
        $xp->importStylesheet($xsl);

        $xml_doc = new DomDocument;
        //echo "loading ", "files/original/".$file->filename, "<br/>";
        $xml_doc->load("files/original/".$file->filename);

        try { 
            if ($doc = $xp->transformToXML($xml_doc)) {         
                return $doc;
        }
        } catch (Exception $e){
        $this->view->error = $e->getMessage();
        }
    }

    function hookPublicHead($args){
        queue_css_url('//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css');
        
    }

}
