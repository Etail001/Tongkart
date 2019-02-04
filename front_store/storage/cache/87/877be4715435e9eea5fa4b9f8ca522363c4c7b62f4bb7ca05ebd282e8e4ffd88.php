<?php

/* techeco/template/common/footer.twig */
class __TwigTemplate_43383700b6e531af8fdab86e6ea8f06775a4525dab7224158e397d3e71a8ba09 extends Twig_Template
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
        echo "<footer>

<div class=\"container\">
  <div class=\"row\">
    <div class=\"newsletter col-xs-12\">
      ";
        // line 6
        echo (isset($context["ftop_full"]) ? $context["ftop_full"] : null);
        echo "
    </div>
    </div>
</div>
  <div class=\"container\">
    <div class=\"row middle-footer\">
      
    ";
        // line 13
        echo (isset($context["footer_left"]) ? $context["footer_left"] : null);
        echo "

    ";
        // line 15
        if (((isset($context["footer_left"]) ? $context["footer_left"] : null) && (isset($context["footer_right"]) ? $context["footer_right"] : null))) {
            // line 16
            echo "    ";
            $context["class"] = "col-sm-8";
            // line 17
            echo "    ";
        } elseif (((isset($context["footer_left"]) ? $context["footer_left"] : null) || (isset($context["footer_right"]) ? $context["footer_right"] : null))) {
            // line 18
            echo "    ";
            $context["class"] = "col-sm-9";
            // line 19
            echo "    ";
        } else {
            // line 20
            echo "    ";
            $context["class"] = "col-sm-12";
            // line 21
            echo "    ";
        }
        // line 22
        echo "<div class=\"col-sm-12 col-md-9 col-xs-12\">
    <div class=\"row\">
      ";
        // line 24
        if ((isset($context["informations"]) ? $context["informations"] : null)) {
            // line 25
            echo "      <div class=\"col-sm-3 col-xs-12 fborder\">
        <h5>";
            // line 26
            echo (isset($context["text_information"]) ? $context["text_information"] : null);
            echo "
          <button type=\"button\" class=\"btn toggle collapsed\" data-toggle=\"collapse\" data-target=\"#info\"></button>
        </h5>
        <div id=\"info\" class=\"collapse footer-collapse\">
        <ul class=\"list-unstyled\">
         ";
            // line 31
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable((isset($context["informations"]) ? $context["informations"] : null));
            foreach ($context['_seq'] as $context["_key"] => $context["information"]) {
                // line 32
                echo "          <li><a href=\"";
                echo $this->getAttribute($context["information"], "href", array());
                echo "\">";
                echo $this->getAttribute($context["information"], "title", array());
                echo "</a></li>
          ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['information'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 34
            echo "           <li><a href=\"";
            echo (isset($context["manufacturer"]) ? $context["manufacturer"] : null);
            echo "\">";
            echo (isset($context["text_manufacturer"]) ? $context["text_manufacturer"] : null);
            echo "</a></li>
        </ul>
        </div>
      </div>
      ";
        }
        // line 39
        echo "      <div class=\"col-sm-3 col-xs-12 fborder\">
        <h5>";
        // line 40
        echo (isset($context["text_account"]) ? $context["text_account"] : null);
        echo "
          <button type=\"button\" class=\"btn toggle collapsed\" data-toggle=\"collapse\" data-target=\"#account\"></button>
        </h5>
        <div id=\"account\" class=\"collapse footer-collapse\">
        <ul class=\"list-unstyled lastb\">
          <li><a href=\"";
        // line 45
        echo (isset($context["account"]) ? $context["account"] : null);
        echo "\">";
        echo (isset($context["text_account"]) ? $context["text_account"] : null);
        echo "</a></li>
          <li><a href=\"";
        // line 46
        echo (isset($context["order"]) ? $context["order"] : null);
        echo "\">";
        echo (isset($context["text_order"]) ? $context["text_order"] : null);
        echo "</a></li>
          <li><a href=\"";
        // line 47
        echo (isset($context["wishlist"]) ? $context["wishlist"] : null);
        echo "\">";
        echo (isset($context["text_wishlist"]) ? $context["text_wishlist"] : null);
        echo "</a></li>
          <li><a href=\"";
        // line 48
        echo (isset($context["newsletter"]) ? $context["newsletter"] : null);
        echo "\">";
        echo (isset($context["text_newsletter"]) ? $context["text_newsletter"] : null);
        echo "</a></li>
          <li><a href=\"";
        // line 49
        echo (isset($context["special"]) ? $context["special"] : null);
        echo "\">";
        echo (isset($context["text_special"]) ? $context["text_special"] : null);
        echo "</a></li>
        </ul>
        </div>
      </div>
      <div class=\"col-sm-3 col-xs-12 fborder\">
        <h5>";
        // line 54
        echo (isset($context["text_service"]) ? $context["text_service"] : null);
        echo "
          <button type=\"button\" class=\"btn  toggle collapsed\" data-toggle=\"collapse\" data-target=\"#service\"></button>
        </h5>
        <div id=\"service\" class=\"collapse footer-collapse\">
        <ul class=\"list-unstyled lastb\">
          <li><a href=\"";
        // line 59
        echo (isset($context["contact"]) ? $context["contact"] : null);
        echo "\">";
        echo (isset($context["text_contact"]) ? $context["text_contact"] : null);
        echo "</a></li>
          <li><a href=\"";
        // line 60
        echo (isset($context["return"]) ? $context["return"] : null);
        echo "\">";
        echo (isset($context["text_return"]) ? $context["text_return"] : null);
        echo "</a></li>
          <li><a href=\"";
        // line 61
        echo (isset($context["sitemap"]) ? $context["sitemap"] : null);
        echo "\">";
        echo (isset($context["text_sitemap"]) ? $context["text_sitemap"] : null);
        echo "</a></li>
          <li><a href=\"";
        // line 62
        echo (isset($context["voucher"]) ? $context["voucher"] : null);
        echo "\">";
        echo (isset($context["text_voucher"]) ? $context["text_voucher"] : null);
        echo "</a></li>
          <li><a href=\"";
        // line 63
        echo (isset($context["affiliate"]) ? $context["affiliate"] : null);
        echo "\">";
        echo (isset($context["text_affiliate"]) ? $context["text_affiliate"] : null);
        echo "</a></li>
        </ul>
        </div>
      </div>
      <div class=\"col-sm-3 col-xs-12 fborder\"> ";
        // line 67
        echo (isset($context["footer_right"]) ? $context["footer_right"] : null);
        echo "</div>
    </div>
      </div>

    </div>
  </div>
   <div class=\"copy text-center\">
      ";
        // line 74
        echo (isset($context["powered"]) ? $context["powered"] : null);
        echo "
    </div>
</footer>
<a href=\"\" id=\"scroll\" title=\"Scroll to Top\" style=\"display: block;\">
    <i class=\"fa fa-angle-up\"></i>
</a>
      ";
        // line 80
        echo (isset($context["fbottom_full"]) ? $context["fbottom_full"] : null);
        echo "


";
        // line 83
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["scripts"]) ? $context["scripts"] : null));
        foreach ($context['_seq'] as $context["_key"] => $context["script"]) {
            // line 84
            echo "<script src=\"";
            echo $context["script"];
            echo "\" type=\"text/javascript\"></script>
";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['script'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 86
        echo "<!--
OpenCart is open source software and you are free to remove the powered by OpenCart if you want, but its generally accepted practise to make a small donation.
Please donate via PayPal to donate@opencart.com
//-->
</body></html>
";
    }

    public function getTemplateName()
    {
        return "techeco/template/common/footer.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  228 => 86,  219 => 84,  215 => 83,  209 => 80,  200 => 74,  190 => 67,  181 => 63,  175 => 62,  169 => 61,  163 => 60,  157 => 59,  149 => 54,  139 => 49,  133 => 48,  127 => 47,  121 => 46,  115 => 45,  107 => 40,  104 => 39,  93 => 34,  82 => 32,  78 => 31,  70 => 26,  67 => 25,  65 => 24,  61 => 22,  58 => 21,  55 => 20,  52 => 19,  49 => 18,  46 => 17,  43 => 16,  41 => 15,  36 => 13,  26 => 6,  19 => 1,);
    }
}
/* <footer>*/
/* */
/* <div class="container">*/
/*   <div class="row">*/
/*     <div class="newsletter col-xs-12">*/
/*       {{ ftop_full }}*/
/*     </div>*/
/*     </div>*/
/* </div>*/
/*   <div class="container">*/
/*     <div class="row middle-footer">*/
/*       */
/*     {{ footer_left }}*/
/* */
/*     {% if footer_left and footer_right %}*/
/*     {% set class = 'col-sm-8' %}*/
/*     {% elseif footer_left or footer_right %}*/
/*     {% set class = 'col-sm-9' %}*/
/*     {% else %}*/
/*     {% set class = 'col-sm-12' %}*/
/*     {% endif %}*/
/* <div class="col-sm-12 col-md-9 col-xs-12">*/
/*     <div class="row">*/
/*       {% if informations %}*/
/*       <div class="col-sm-3 col-xs-12 fborder">*/
/*         <h5>{{ text_information }}*/
/*           <button type="button" class="btn toggle collapsed" data-toggle="collapse" data-target="#info"></button>*/
/*         </h5>*/
/*         <div id="info" class="collapse footer-collapse">*/
/*         <ul class="list-unstyled">*/
/*          {% for information in informations %}*/
/*           <li><a href="{{ information.href }}">{{ information.title }}</a></li>*/
/*           {% endfor %}*/
/*            <li><a href="{{ manufacturer }}">{{ text_manufacturer }}</a></li>*/
/*         </ul>*/
/*         </div>*/
/*       </div>*/
/*       {% endif %}*/
/*       <div class="col-sm-3 col-xs-12 fborder">*/
/*         <h5>{{ text_account }}*/
/*           <button type="button" class="btn toggle collapsed" data-toggle="collapse" data-target="#account"></button>*/
/*         </h5>*/
/*         <div id="account" class="collapse footer-collapse">*/
/*         <ul class="list-unstyled lastb">*/
/*           <li><a href="{{ account }}">{{ text_account }}</a></li>*/
/*           <li><a href="{{ order }}">{{ text_order }}</a></li>*/
/*           <li><a href="{{ wishlist }}">{{ text_wishlist }}</a></li>*/
/*           <li><a href="{{ newsletter }}">{{ text_newsletter }}</a></li>*/
/*           <li><a href="{{ special }}">{{ text_special }}</a></li>*/
/*         </ul>*/
/*         </div>*/
/*       </div>*/
/*       <div class="col-sm-3 col-xs-12 fborder">*/
/*         <h5>{{ text_service }}*/
/*           <button type="button" class="btn  toggle collapsed" data-toggle="collapse" data-target="#service"></button>*/
/*         </h5>*/
/*         <div id="service" class="collapse footer-collapse">*/
/*         <ul class="list-unstyled lastb">*/
/*           <li><a href="{{ contact }}">{{ text_contact }}</a></li>*/
/*           <li><a href="{{ return }}">{{ text_return }}</a></li>*/
/*           <li><a href="{{ sitemap }}">{{ text_sitemap }}</a></li>*/
/*           <li><a href="{{ voucher }}">{{ text_voucher }}</a></li>*/
/*           <li><a href="{{ affiliate }}">{{ text_affiliate }}</a></li>*/
/*         </ul>*/
/*         </div>*/
/*       </div>*/
/*       <div class="col-sm-3 col-xs-12 fborder"> {{ footer_right }}</div>*/
/*     </div>*/
/*       </div>*/
/* */
/*     </div>*/
/*   </div>*/
/*    <div class="copy text-center">*/
/*       {{ powered }}*/
/*     </div>*/
/* </footer>*/
/* <a href="" id="scroll" title="Scroll to Top" style="display: block;">*/
/*     <i class="fa fa-angle-up"></i>*/
/* </a>*/
/*       {{ fbottom_full }}*/
/* */
/* */
/* {% for script in scripts %}*/
/* <script src="{{ script }}" type="text/javascript"></script>*/
/* {% endfor %}*/
/* <!--*/
/* OpenCart is open source software and you are free to remove the powered by OpenCart if you want, but its generally accepted practise to make a small donation.*/
/* Please donate via PayPal to donate@opencart.com*/
/* //-->*/
/* </body></html>*/
/* */
