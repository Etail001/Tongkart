<?php

/* techeco/template/extension/module/webinewsletter/default.twig */
class __TwigTemplate_a0942fc42c24b74ad9103eb1ac00db7478c80fc1d16ef1d392d6baa56f4beae6 extends Twig_Template
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
        echo " webi-newsletter row\" id=\"newsletter_";
        echo (isset($context["id"]) ? $context["id"] : null);
        echo "\" data-mode=\"";
        echo (isset($context["mode"]) ? $context["mode"] : null);
        echo "\">
\t\t<form id=\"formNewLestter\" method=\"post\" action=\"";
        // line 2
        echo (isset($context["action"]) ? $context["action"] : null);
        echo "\" class=\"formNewLestter\" ";
        if ( !twig_test_empty((isset($context["banner"]) ? $context["banner"] : null))) {
            echo " style=\"background-image: url('";
            echo (isset($context["banner"]) ? $context["banner"] : null);
            echo "')\" ";
        }
        echo ">
\t\t\t<div class=\"inner\">
\t\t\t\t<!-- <h3 >";
        // line 4
        echo (isset($context["entry_newsletter"]) ? $context["entry_newsletter"] : null);
        echo "</h3> -->
\t\t\t\t<div class=\"description-top col-lg-6 col-md-6 col-sm-6  col-xs-12 bestlc hidden-xs\">
\t\t\t\t\t<p>";
        // line 6
        echo (isset($context["description"]) ? $context["description"] : null);
        echo "</p>
\t\t\t\t</div>
\t\t\t\t<div class=\"col-lg-6 col-md-6 col-sm-6  col-xs-12 bestrc\">
\t\t\t\t<div class=\"form-group\">
\t\t\t\t\t<input type=\"text\" class=\"form-control input-md inputNew\" ";
        // line 10
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
\t\t\t\t\t<button type=\"submit\" name=\"submitNewsletter\" class=\"btn btn-danger\">";
        // line 13
        echo (isset($context["button_subscribe"]) ? $context["button_subscribe"] : null);
        echo "</button>
\t\t\t\t</div>\t
\t\t\t\t<input type=\"hidden\" value=\"1\" name=\"action\">
\t\t\t\t<div class=\"valid\"></div>
\t\t\t\t<div class=\"description-bottom\">
\t\t\t\t\t";
        // line 18
        echo (isset($context["social"]) ? $context["social"] : null);
        echo "
\t\t\t\t</div>
\t\t\t</div>
\t\t\t</div>
\t\t</form>
</div>

<script type=\"text/javascript\"><!--
\$(\"#newsletter_";
        // line 26
        echo (isset($context["id"]) ? $context["id"] : null);
        echo "\").webiNewsletter().work(  '";
        echo (isset($context["valid_email"]) ? $context["valid_email"] : null);
        echo "' );
--></script>";
    }

    public function getTemplateName()
    {
        return "techeco/template/extension/module/webinewsletter/default.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  84 => 26,  73 => 18,  65 => 13,  51 => 10,  44 => 6,  39 => 4,  28 => 2,  19 => 1,);
    }
}
/* <div class="webi-newsletter {{ prefix }} webi-newsletter row" id="newsletter_{{ id }}" data-mode="{{ mode }}">*/
/* 		<form id="formNewLestter" method="post" action="{{ action }}" class="formNewLestter" {% if banner is not empty%} style="background-image: url('{{ banner }}')" {% endif %}>*/
/* 			<div class="inner">*/
/* 				<!-- <h3 >{{ entry_newsletter }}</h3> -->*/
/* 				<div class="description-top col-lg-6 col-md-6 col-sm-6  col-xs-12 bestlc hidden-xs">*/
/* 					<p>{{ description }}</p>*/
/* 				</div>*/
/* 				<div class="col-lg-6 col-md-6 col-sm-6  col-xs-12 bestrc">*/
/* 				<div class="form-group">*/
/* 					<input type="text" class="form-control input-md inputNew" {% if customer_email is not defined %} onblur="javascript:if(this.value=='')this.value='{{ default_input_text }}';" onfocus="javascript:if(this.value=='{{ default_input_text }}')this.value='';"{% endif %} value="{{ customer_email? customer_email : default_input_text }}" size="18" name="email">*/
/* 				</div>*/
/* 				<div class="button-submit">*/
/* 					<button type="submit" name="submitNewsletter" class="btn btn-danger">{{ button_subscribe }}</button>*/
/* 				</div>	*/
/* 				<input type="hidden" value="1" name="action">*/
/* 				<div class="valid"></div>*/
/* 				<div class="description-bottom">*/
/* 					{{ social }}*/
/* 				</div>*/
/* 			</div>*/
/* 			</div>*/
/* 		</form>*/
/* </div>*/
/* */
/* <script type="text/javascript"><!--*/
/* $("#newsletter_{{ id }}").webiNewsletter().work(  '{{ valid_email }}' );*/
/* --></script>*/
