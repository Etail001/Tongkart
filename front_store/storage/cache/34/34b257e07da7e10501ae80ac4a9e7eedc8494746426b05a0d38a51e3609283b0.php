<?php

/* techeco/template/common/menu.twig */
class __TwigTemplate_ff2b9fc3a62a5e7fa859822c95b8583e0967200c1579b26218240d9fa8d75ac7 extends Twig_Template
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
        echo "<div class=\"hidden-md hidden-lg hidden-sm mobilemenu\">
";
        // line 2
        if ((isset($context["categories"]) ? $context["categories"] : null)) {
            // line 3
            echo "<div class=\"horizontal-menu\">
  <nav id=\"menu\" class=\"navbar\">

    <div class=\"navbar-header\">
      <button type=\"button\" class=\"btn btn-navbar navbar-toggle\" onclick=\"openNav()\" data-toggle=\"collapse\" data-target=\".navbar-ex1-collapse\"><i class=\"fa fa-bars\"></i></button>
    </div>

<div id=\"mySidenav\" class=\"sidenav\">
 <div class=\"close-nav\">
                <span class=\"categories\">Category</span>
                <a href=\"javascript:void(0)\" class=\"closebtn pull-right\" onclick=\"closeNav()\"><i class=\"fa fa-close\"></i></a>
            </div>
    <div class=\"collapse navbar-collapse navbar-ex1-collapse\">
      <ul class=\"nav navbar-nav\">
        ";
            // line 17
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable((isset($context["categories"]) ? $context["categories"] : null));
            foreach ($context['_seq'] as $context["_key"] => $context["category"]) {
                // line 18
                echo "        ";
                if ($this->getAttribute($context["category"], "children", array())) {
                    // line 19
                    echo "        <li class=\"dropdown\"><a href=\"";
                    echo $this->getAttribute($context["category"], "href", array());
                    echo "\" class=\"dropdown-toggle header-menu\" data-toggle=\"dropdown\">";
                    echo $this->getAttribute($context["category"], "name", array());
                    echo "<i class=\"fa fa-angle-down pull-right\"></i></a>
          <div class=\"dropdown-menu\">
            <div class=\"dropdown-inner\"> ";
                    // line 21
                    $context['_parent'] = $context;
                    $context['_seq'] = twig_ensure_traversable(twig_array_batch($this->getAttribute($context["category"], "children", array()), (twig_length_filter($this->env, $this->getAttribute($context["category"], "children", array())) / twig_round($this->getAttribute($context["category"], "column", array()), 1, "ceil"))));
                    foreach ($context['_seq'] as $context["_key"] => $context["children"]) {
                        // line 22
                        echo "              <ul class=\"list-unstyled\">
                ";
                        // line 23
                        $context['_parent'] = $context;
                        $context['_seq'] = twig_ensure_traversable($context["children"]);
                        foreach ($context['_seq'] as $context["_key"] => $context["child"]) {
                            // line 24
                            echo "                  <!--3rd level-->
                    <li class=\"dropdown-submenu\"> <a href=\"";
                            // line 25
                            echo $this->getAttribute($context["child"], "href", array());
                            echo "\" class=\"submenu-title\"> ";
                            echo $this->getAttribute($context["child"], "name", array());
                            echo " </a>
                      ";
                            // line 26
                            if ($this->getAttribute($context["child"], "grand_childs", array())) {
                                // line 27
                                echo "                      <ul class=\"list-unstyled grand-child\">
                        ";
                                // line 28
                                $context['_parent'] = $context;
                                $context['_seq'] = twig_ensure_traversable($this->getAttribute($context["child"], "grand_childs", array()));
                                foreach ($context['_seq'] as $context["_key"] => $context["grand_child"]) {
                                    // line 29
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
                                // line 31
                                echo "                      </ul>
                      ";
                            }
                            // line 33
                            echo "                    </li>
                    <!--3rd level over-->
                ";
                        }
                        $_parent = $context['_parent'];
                        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['child'], $context['_parent'], $context['loop']);
                        $context = array_intersect_key($context, $_parent) + $_parent;
                        // line 36
                        echo "              </ul>
              ";
                    }
                    $_parent = $context['_parent'];
                    unset($context['_seq'], $context['_iterated'], $context['_key'], $context['children'], $context['_parent'], $context['loop']);
                    $context = array_intersect_key($context, $_parent) + $_parent;
                    // line 37
                    echo "</div>
          <!--   <a href=\"";
                    // line 38
                    echo $this->getAttribute($context["category"], "href", array());
                    echo "\" class=\"see-all\">";
                    echo (isset($context["text_all"]) ? $context["text_all"] : null);
                    echo " ";
                    echo $this->getAttribute($context["category"], "name", array());
                    echo "</a>  --></div>
        </li>
        ";
                } else {
                    // line 41
                    echo "        <li><a href=\"";
                    echo $this->getAttribute($context["category"], "href", array());
                    echo "\">";
                    echo $this->getAttribute($context["category"], "name", array());
                    echo "</a></li>
        ";
                }
                // line 43
                echo "        ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['category'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 44
            echo "      </ul>
    </div>
  </nav>
</div>
</div>
";
        }
        // line 50
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
        return "techeco/template/common/menu.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  149 => 50,  141 => 44,  135 => 43,  127 => 41,  117 => 38,  114 => 37,  107 => 36,  99 => 33,  95 => 31,  84 => 29,  80 => 28,  77 => 27,  75 => 26,  69 => 25,  66 => 24,  62 => 23,  59 => 22,  55 => 21,  47 => 19,  44 => 18,  40 => 17,  24 => 3,  22 => 2,  19 => 1,);
    }
}
/* <div class="hidden-md hidden-lg hidden-sm mobilemenu">*/
/* {% if categories %}*/
/* <div class="horizontal-menu">*/
/*   <nav id="menu" class="navbar">*/
/* */
/*     <div class="navbar-header">*/
/*       <button type="button" class="btn btn-navbar navbar-toggle" onclick="openNav()" data-toggle="collapse" data-target=".navbar-ex1-collapse"><i class="fa fa-bars"></i></button>*/
/*     </div>*/
/* */
/* <div id="mySidenav" class="sidenav">*/
/*  <div class="close-nav">*/
/*                 <span class="categories">Category</span>*/
/*                 <a href="javascript:void(0)" class="closebtn pull-right" onclick="closeNav()"><i class="fa fa-close"></i></a>*/
/*             </div>*/
/*     <div class="collapse navbar-collapse navbar-ex1-collapse">*/
/*       <ul class="nav navbar-nav">*/
/*         {% for category in categories %}*/
/*         {% if category.children %}*/
/*         <li class="dropdown"><a href="{{ category.href }}" class="dropdown-toggle header-menu" data-toggle="dropdown">{{ category.name }}<i class="fa fa-angle-down pull-right"></i></a>*/
/*           <div class="dropdown-menu">*/
/*             <div class="dropdown-inner"> {% for children in category.children|batch(category.children|length / category.column|round(1, 'ceil')) %}*/
/*               <ul class="list-unstyled">*/
/*                 {% for child in children %}*/
/*                   <!--3rd level-->*/
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
/*           <!--   <a href="{{ category.href }}" class="see-all">{{ text_all }} {{ category.name }}</a>  --></div>*/
/*         </li>*/
/*         {% else %}*/
/*         <li><a href="{{ category.href }}">{{ category.name }}</a></li>*/
/*         {% endif %}*/
/*         {% endfor %}*/
/*       </ul>*/
/*     </div>*/
/*   </nav>*/
/* </div>*/
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
