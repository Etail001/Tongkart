<?php

/* techeco/template/extension/module/webinewsletter/popup.twig */
class __TwigTemplate_c2701711127539c262b0384f8db1d941c70febeced1bd67ca044d710def2392f extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<div class=\"webi-newsletter ";
        echo (isset($context["prefix"]) ? $context["prefix"] : null);
        echo " webi-newsletter hide\" id=\"newsletter_";
        echo (isset($context["id"]) ? $context["id"] : null);
        echo "\" data-mode=\"";
        echo (isset($context["mode"]) ? $context["mode"] : null);
        echo "\">
\t\t<form id=\"formNewLestter\" method=\"post\" action=\"";
        // line 2
        echo (isset($context["action"]) ? $context["action"] : null);
        echo "\" class=\"formNewLestter  white-popup newsletter-bg news-popup\" ";
        if ( !twig_test_empty((isset($context["banner"]) ? $context["banner"] : null))) {
            echo " style=\"background-image: url('";
            echo (isset($context["banner"]) ? $context["banner"] : null);
            echo "')\" ";
        }
        echo ">
\t\t\t<div class=\"inner\">
\t\t\t";
        // line 5
        echo "\t\t\t\t<div class=\"description-top\">
\t\t\t\t\t<p>";
        // line 6
        echo (isset($context["description"]) ? $context["description"] : null);
        echo "</p>
\t\t\t\t</div>
\t\t\t\t<div class=\"form-group\">
\t\t\t\t\t<input type=\"text\" class=\"form-control input-md inputNew\" ";
        // line 9
        if ( !array_key_exists("customer_email", $context)) {
            echo " onblur=\"javascript:if(this.value=='')this.value='";
            echo (isset($context["default_input_text"]) ? $context["default_input_text"] : null);
            echo "';\" onfocus=\"javascript:if(this.value=='";
            echo (isset($context["default_input_text"]) ? $context["default_input_text"] : null);
            echo "')this.value='';\"";
        }
        echo " value=\"";
        echo (((isset($context["customer_email"]) ? $context["customer_email"] : null)) ? ((isset($context["customer_email"]) ? $context["customer_email"] : null)) : ((isset($context["default_input_text"]) ? $context["default_input_text"] : null)));
        echo "\" size=\"18\" name=\"email\">
\t\t\t\t</div>
\t\t\t\t<div class=\"button-submit\">
\t\t\t\t\t<button type=\"submit\" name=\"submitNewsletter\" class=\"btn btn-primary\">";
        // line 12
        echo (isset($context["button_subscribe"]) ? $context["button_subscribe"] : null);
        echo "</button>
\t\t\t\t</div>\t
\t\t\t\t<input type=\"hidden\" value=\"1\" name=\"action\">
\t\t\t\t<div class=\"valid\"></div>
\t\t\t\t<div class=\"description-bottom\">
\t\t\t\t\t";
        // line 17
        echo (isset($context["social"]) ? $context["social"] : null);
        echo "
\t\t\t\t</div>
\t\t\t</div>
\t\t\t<div class=\"form-group\">
\t\t\t\t<div class=\"checkbox\">
\t\t\t    <label><input type=\"checkbox\" checked=\"false\"> ";
        // line 22
        echo (isset($context["text_dont_show"]) ? $context["text_dont_show"] : null);
        echo " </label>
\t\t\t  </div>
\t\t\t</div>\t
\t\t</form>
</div>

<script type=\"text/javascript\"><!--
\$(\"#newsletter_";
        // line 29
        echo (isset($context["id"]) ? $context["id"] : null);
        echo "\").webiNewsletter().work(  '";
        echo (isset($context["valid_email"]) ? $context["valid_email"] : null);
        echo "' );
--></script>";
    }

    public function getTemplateName()
    {
        return "techeco/template/extension/module/webinewsletter/popup.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  88 => 29,  78 => 22,  70 => 17,  62 => 12,  48 => 9,  42 => 6,  39 => 5,  28 => 2,  19 => 1,);
    }
}
/* <div class="webi-newsletter {{ prefix }} webi-newsletter hide" id="newsletter_{{ id }}" data-mode="{{ mode }}">*/
/* 		<form id="formNewLestter" method="post" action="{{ action }}" class="formNewLestter  white-popup newsletter-bg news-popup" {% if banner is not empty%} style="background-image: url('{{ banner }}')" {% endif %}>*/
/* 			<div class="inner">*/
/* 			{# 	<h3 >{{ entry_newsletter }}</h3> #}*/
/* 				<div class="description-top">*/
/* 					<p>{{ description }}</p>*/
/* 				</div>*/
/* 				<div class="form-group">*/
/* 					<input type="text" class="form-control input-md inputNew" {% if customer_email is not defined %} onblur="javascript:if(this.value=='')this.value='{{ default_input_text }}';" onfocus="javascript:if(this.value=='{{ default_input_text }}')this.value='';"{% endif %} value="{{ customer_email? customer_email : default_input_text }}" size="18" name="email">*/
/* 				</div>*/
/* 				<div class="button-submit">*/
/* 					<button type="submit" name="submitNewsletter" class="btn btn-primary">{{ button_subscribe }}</button>*/
/* 				</div>	*/
/* 				<input type="hidden" value="1" name="action">*/
/* 				<div class="valid"></div>*/
/* 				<div class="description-bottom">*/
/* 					{{ social }}*/
/* 				</div>*/
/* 			</div>*/
/* 			<div class="form-group">*/
/* 				<div class="checkbox">*/
/* 			    <label><input type="checkbox" checked="false"> {{ text_dont_show }} </label>*/
/* 			  </div>*/
/* 			</div>	*/
/* 		</form>*/
/* </div>*/
/* */
/* <script type="text/javascript"><!--*/
/* $("#newsletter_{{ id }}").webiNewsletter().work(  '{{ valid_email }}' );*/
/* --></script>*/
