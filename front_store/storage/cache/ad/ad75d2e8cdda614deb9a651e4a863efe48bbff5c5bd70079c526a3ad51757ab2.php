<?php

/* techeco/template/extension/module/category.twig */
class __TwigTemplate_3b39830a450a414c2c88dec260cfffb9a55e8187bbf79ee4aecd995fdbc3ee16 extends Twig_Template
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
        echo "<div class=\"hidden-xs\">
";
        // line 2
        if ((isset($context["categories"]) ? $context["categories"] : null)) {
            // line 3
            echo "<div class=\"cate-menu \">
  <nav id=\"menu\" class=\"navbar\">
    <div class=\"navbar-header\">
      <button type=\"button\" class=\"btn btn-navbar navbar-toggle\" data-toggle=\"collapse\" data-target=\".navbar-ex1-collapse\"><i class=\"fa fa-bars\"></i></button>
    </div>
    <div class=\"allcat\">";
            // line 8
            echo (isset($context["text_category"]) ? $context["text_category"] : null);
            echo "</div>
    <div class=\"collapse navbar-collapse navbar-ex1-collapse\">
      <ul class=\"nav\">
        ";
            // line 11
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable((isset($context["categories"]) ? $context["categories"] : null));
            foreach ($context['_seq'] as $context["_key"] => $context["category"]) {
                // line 12
                echo "        ";
                if ($this->getAttribute($context["category"], "children", array())) {
                    // line 13
                    echo "        <li class=\"dropdown my-menu\"><a href=\"";
                    echo $this->getAttribute($context["category"], "href", array());
                    echo "\" class=\"header-menu\">
          <div class=\"thumb_img pull-left\">
            <img src=\"";
                    // line 15
                    echo $this->getAttribute($context["category"], "thumb_menu", array());
                    echo "\" alt=\"";
                    echo $this->getAttribute($context["category"], "name", array());
                    echo "\">
          </div>
          ";
                    // line 17
                    echo $this->getAttribute($context["category"], "name", array());
                    echo "<i class=\"fa fa-angle-down enangle pull-right\"></i></a>
          <div class=\"dropdown-menu\">
            <div class=\"dropdown-inner\">
            ";
                    // line 20
                    $context['_parent'] = $context;
                    $context['_seq'] = twig_ensure_traversable(twig_array_batch($this->getAttribute($context["category"], "children", array()), (twig_length_filter($this->env, $this->getAttribute($context["category"], "children", array())) / twig_round($this->getAttribute($context["category"], "column", array()), 1, "ceil"))));
                    foreach ($context['_seq'] as $context["_key"] => $context["children"]) {
                        // line 21
                        echo "              <ul class=\"list-unstyled\">
                ";
                        // line 22
                        $context['_parent'] = $context;
                        $context['_seq'] = twig_ensure_traversable($context["children"]);
                        foreach ($context['_seq'] as $context["_key"] => $context["child"]) {
                            // line 23
                            echo "                 <!--3rd level-->
                    <li class=\"dropdown-submenu\"> <a href=\"";
                            // line 24
                            echo $this->getAttribute($context["child"], "href", array());
                            echo "\" class=\"submenu-title\"> ";
                            echo $this->getAttribute($context["child"], "name", array());
                            echo " </a>
                      ";
                            // line 25
                            if ($this->getAttribute($context["child"], "grand_childs", array())) {
                                // line 26
                                echo "                      <ul class=\"list-unstyled grand-child\">
                        ";
                                // line 27
                                $context['_parent'] = $context;
                                $context['_seq'] = twig_ensure_traversable($this->getAttribute($context["child"], "grand_childs", array()));
                                foreach ($context['_seq'] as $context["_key"] => $context["grand_child"]) {
                                    // line 28
                                    echo "                        <li> <a href=\"";
                                    echo $this->getAttribute($context["grand_child"], "href", array());
                                    echo "\"> ";
                                    echo $this->getAttribute($context["grand_child"], "name", array());
                                    echo " </a> </li>
                        ";
                                }
                                $_parent = $context['_parent'];
                                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['grand_child'], $context['_parent'], $context['loop']);
                                $context = array_intersect_key($context, $_parent) + $_parent;
                                // line 30
                                echo "                      </ul>
                      ";
                            }
                            // line 32
                            echo "                    </li>
                    <!--3rd level over-->
                ";
                        }
                        $_parent = $context['_parent'];
                        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['child'], $context['_parent'], $context['loop']);
                        $context = array_intersect_key($context, $_parent) + $_parent;
                        // line 35
                        echo "              </ul>
              ";
                    }
                    $_parent = $context['_parent'];
                    unset($context['_seq'], $context['_iterated'], $context['_key'], $context['children'], $context['_parent'], $context['loop']);
                    $context = array_intersect_key($context, $_parent) + $_parent;
                    // line 36
                    echo "</div>
            <!-- <a href=\"";
                    // line 37
                    echo $this->getAttribute($context["category"], "href", array());
                    echo "\" class=\"see-all\">";
                    echo (isset($context["text_all"]) ? $context["text_all"] : null);
                    echo " ";
                    echo $this->getAttribute($context["category"], "name", array());
                    echo "</a> --> </div>
        </li>
        ";
                } else {
                    // line 40
                    echo "        <li class=\"my-menu\"><a href=\"";
                    echo $this->getAttribute($context["category"], "href", array());
                    echo "\">
        <div class=\"thumb_img pull-left\">
            <img src=\"";
                    // line 42
                    echo $this->getAttribute($context["category"], "thumb_menu", array());
                    echo "\" alt=\"";
                    echo $this->getAttribute($context["category"], "name", array());
                    echo "\">
          </div>
        ";
                    // line 44
                    echo $this->getAttribute($context["category"], "name", array());
                    echo "</a></li>
        ";
                }
                // line 46
                echo "        ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['category'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 47
            echo "      </ul>
    </div>
  </nav>
</div>
";
        }
        // line 52
        echo "</div>

<script type=\"text/javascript\">
 function headermenu() {
     if (jQuery(window).width() < 768)
     {
         jQuery('ul.nav li.dropdown a.header-menu').attr(\"data-toggle\",\"dropdown\");        
     }
     else
     {
         jQuery('ul.nav li.dropdown a.header-menu').attr(\"data-toggle\",\"\"); 
     }
}
\$(document).ready(function(){headermenu();});
jQuery(window).resize(function() {headermenu();});
jQuery(window).scroll(function() {headermenu();});
</script>";
    }

    public function getTemplateName()
    {
        return "techeco/template/extension/module/category.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  166 => 52,  159 => 47,  153 => 46,  148 => 44,  141 => 42,  135 => 40,  125 => 37,  122 => 36,  115 => 35,  107 => 32,  103 => 30,  92 => 28,  88 => 27,  85 => 26,  83 => 25,  77 => 24,  74 => 23,  70 => 22,  67 => 21,  63 => 20,  57 => 17,  50 => 15,  44 => 13,  41 => 12,  37 => 11,  31 => 8,  24 => 3,  22 => 2,  19 => 1,);
    }
}
/* <div class="hidden-xs">*/
/* {% if categories %}*/
/* <div class="cate-menu ">*/
/*   <nav id="menu" class="navbar">*/
/*     <div class="navbar-header">*/
/*       <button type="button" class="btn btn-navbar navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse"><i class="fa fa-bars"></i></button>*/
/*     </div>*/
/*     <div class="allcat">{{ text_category }}</div>*/
/*     <div class="collapse navbar-collapse navbar-ex1-collapse">*/
/*       <ul class="nav">*/
/*         {% for category in categories %}*/
/*         {% if category.children %}*/
/*         <li class="dropdown my-menu"><a href="{{ category.href }}" class="header-menu">*/
/*           <div class="thumb_img pull-left">*/
/*             <img src="{{ category.thumb_menu }}" alt="{{ category.name }}">*/
/*           </div>*/
/*           {{ category.name }}<i class="fa fa-angle-down enangle pull-right"></i></a>*/
/*           <div class="dropdown-menu">*/
/*             <div class="dropdown-inner">*/
/*             {% for children in category.children|batch(category.children|length / category.column|round(1, 'ceil')) %}*/
/*               <ul class="list-unstyled">*/
/*                 {% for child in children %}*/
/*                  <!--3rd level-->*/
/*                     <li class="dropdown-submenu"> <a href="{{ child.href }}" class="submenu-title"> {{ child.name }} </a>*/
/*                       {% if child.grand_childs %}*/
/*                       <ul class="list-unstyled grand-child">*/
/*                         {% for grand_child in child.grand_childs %}*/
/*                         <li> <a href="{{ grand_child.href }}"> {{grand_child.name}} </a> </li>*/
/*                         {% endfor %}*/
/*                       </ul>*/
/*                       {% endif %}*/
/*                     </li>*/
/*                     <!--3rd level over-->*/
/*                 {% endfor %}*/
/*               </ul>*/
/*               {% endfor %}</div>*/
/*             <!-- <a href="{{ category.href }}" class="see-all">{{ text_all }} {{ category.name }}</a> --> </div>*/
/*         </li>*/
/*         {% else %}*/
/*         <li class="my-menu"><a href="{{ category.href }}">*/
/*         <div class="thumb_img pull-left">*/
/*             <img src="{{ category.thumb_menu }}" alt="{{ category.name }}">*/
/*           </div>*/
/*         {{ category.name }}</a></li>*/
/*         {% endif %}*/
/*         {% endfor %}*/
/*       </ul>*/
/*     </div>*/
/*   </nav>*/
/* </div>*/
/* {% endif %}*/
/* </div>*/
/* */
/* <script type="text/javascript">*/
/*  function headermenu() {*/
/*      if (jQuery(window).width() < 768)*/
/*      {*/
/*          jQuery('ul.nav li.dropdown a.header-menu').attr("data-toggle","dropdown");        */
/*      }*/
/*      else*/
/*      {*/
/*          jQuery('ul.nav li.dropdown a.header-menu').attr("data-toggle",""); */
/*      }*/
/* }*/
/* $(document).ready(function(){headermenu();});*/
/* jQuery(window).resize(function() {headermenu();});*/
/* jQuery(window).scroll(function() {headermenu();});*/
/* </script>*/
