<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:template name="css_js_header">
        <link href="/images/favicon.png" rel="shortcut icon" type="image/vnd.microsoft.icon"/>
        <link rel="stylesheet" href="/css/camera.css"/>
        <link rel="stylesheet" href="/css/select2.css?v4.0.3"/>
        <link rel="stylesheet" href="/css/style.css?v2.3"/>
        <link rel="stylesheet" href="/css/font-awesome.min.css"/>
        <link rel="stylesheet" href="/css/print.css" media="Print"/>
        <link rel="stylesheet" href="/css/bootstrap.min.css"/>
        <link rel="stylesheet" href="/css/bootstrap-datetimepicker.min.css"/>
        <link rel="stylesheet" href="//cdn.datatables.net/plug-ins/3cfcc339e89/integration/bootstrap/3/dataTables.bootstrap.css"/>
        <script src="/js/jquery.min.js"/>
        <script src="/js/jquery-ui.min.js"/>
        <script src="/js/bootstrap.min.js"/>
        <script src="/js/bootbox.min.js"/>
        <script src="/js/jquery.multiselect.min.js?v4.0.3"/>
        <script src="/js/jquery.maskinput.min.js"/>
        <script src="/js/moment.min.js"/>
        <script src="/js/moment.ru.js"/>
        <script src="/js/bootstrap-datetimepicker.js"/>
        <script src="/js/bootstrap-typeahead.min.js"/>
        <script src="/js/camera.min.js"/>
        <script src="/js/ready.js?v2.4"/>
        <script src="/js/common.js?v3.4"/>
        <script src="/js/script.js?v2.6"/>
        <script src="//cdn.ckeditor.com/4.6.1/full/ckeditor.js"/>
        <script src="//cdn.datatables.net/1.10.4/js/jquery.dataTables.min.js"/>
        <script src="//cdn.datatables.net/plug-ins/3cfcc339e89/integration/bootstrap/3/dataTables.bootstrap.js"/>
        <xsl:text disable-output-escaping="yes">
                <![CDATA[
            <script src="//maps.googleapis.com/maps/api/js?key=AIzaSyAnDrB-qO4i5uCua-4krGQsloWYJBRtgNU&libraries=places"></script>
                ]]>
            </xsl:text>
        <script src="/js/gmap.js?v2.4"/>
        <script>
            $(function(){
            if ($('#edit_content').length){CKEDITOR.replace( 'edit_content');}
            });
        </script>
    </xsl:template>

</xsl:stylesheet>