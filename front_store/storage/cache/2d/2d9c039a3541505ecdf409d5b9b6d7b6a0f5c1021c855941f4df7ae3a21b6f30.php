<?php

/* techeco/template/common/search.twig */
class __TwigTemplate_1a0d8d23ce631a2a896ed7c32cd6d87201cb18033796e715841d3a118ddf68a1 extends Twig_Template
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
        echo "<div class=\"insearch\">
<div id=\"search\" class=\"input-group\">
  <input type=\"text\" name=\"search\" value=\"";
        // line 3
        echo (isset($context["search"]) ? $context["search"] : null);
        echo "\" placeholder=\"";
        echo (isset($context["text_search"]) ? $context["text_search"] : null);
        echo "\" class=\"form-control input-lg\" />
  <span class=\"input-group-btn\">
    <button type=\"button\" class=\"btn btn-default\"><svg width=\"16px\" height=\"15px\"> <use xlink:href=\"#hsearch\"></use></svg></button>
  </span>
</div>
</div>";
    }

    public function getTemplateName()
    {
        return "techeco/template/common/search.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  23 => 3,  19 => 1,);
    }
}
/* <div class="insearch">*/
/* <div id="search" class="input-group">*/
/*   <input type="text" name="search" value="{{ search }}" placeholder="{{ text_search }}" class="form-control input-lg" />*/
/*   <span class="input-group-btn">*/
/*     <button type="button" class="btn btn-default"><svg width="16px" height="15px"> <use xlink:href="#hsearch"></use></svg></button>*/
/*   </span>*/
/* </div>*/
/* </div>*/
