<?php

/* techeco/template/extension/module/blogger.twig */
class __TwigTemplate_4302687b42f7af35619a025446309c54b1ae9e7a13cd13435bbf09629602abe8 extends Twig_Template
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
        echo "    <div class=\"box blog_webi\">
        <h1 class=\"heading\">";
        // line 2
        echo (isset($context["heading_title"]) ? $context["heading_title"] : null);
        echo "</h1>
        <div class=\"box-content\">
            <div class=\"box-product row\">
                <div id=\"blog\" class=\"owl-carousel owl-theme\">
                ";
        // line 6
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["blogs"]) ? $context["blogs"] : null));
        foreach ($context['_seq'] as $context["_key"] => $context["blog"]) {
            // line 7
            echo "                    <div class=\"product-block col-xs-12\">
                        ";
            // line 8
            if ($this->getAttribute($context["blog"], "image", array())) {
                // line 9
                echo "                            <div class=\"blog-left\">
                                <div class=\"webi-blog-image\">
                                    <img src=\"";
                // line 11
                echo $this->getAttribute($context["blog"], "image", array());
                echo "\" alt=\"";
                echo (isset($context["heading_title"]) ? $context["heading_title"] : null);
                echo "\" title=\"";
                echo (isset($context["heading_title"]) ? $context["heading_title"] : null);
                echo "\" class=\"img-responsive\" />
                                    <div class=\"blog-post-image-hover\"> </div>
                                    <div class=\"webi_post_hover\">
                                        <div class=\"blog-ic\">
                                        <a class=\"icon zoom\" title=\"Click to view Full Image \" href=\"";
                // line 15
                echo $this->getAttribute($context["blog"], "image", array());
                echo "\" data-lightbox=\"example-set\"><i class=\"fa fa-search\"></i></a>
                                        <a class=\"icon readmore_link\" title=\"all blog\" href=\"";
                // line 16
                echo (isset($context["all_blogs"]) ? $context["all_blogs"] : null);
                echo "\"><i class=\"fa fa-link\"></i></a>
                                    </div>
                                    </div>
                                </div>
                            </div>
                        ";
            }
            // line 22
            echo "                        <div class=\"blog-right\">
                            <h4><a href=\"";
            // line 23
            echo $this->getAttribute($context["blog"], "href", array());
            echo "\">";
            echo $this->getAttribute($context["blog"], "title", array());
            echo "</a> </h4>
                            <div class=\"webi-post-author\">
                                <i class=\"fa fa-calendar\"></i><span class=\"date-time\">";
            // line 25
            echo $this->getAttribute($context["blog"], "date_added", array());
            echo "</span>
                                 <span class=\"seper\">|</span>
                                 <span class=\"write-comment\"> <a href=\"";
            // line 27
            echo $this->getAttribute($context["blog"], "href", array());
            echo "\">";
            echo $this->getAttribute($context["blog"], "total_comments", array());
            echo " ";
            echo (isset($context["entry_comment"]) ? $context["entry_comment"] : null);
            echo "</a> </span>
                            </div>
                           <!--  <div class=\"blog-desc\">";
            // line 29
            echo $this->getAttribute($context["blog"], "description", array());
            echo "</div> -->
                            <!-- <div class=\"read_link\"> <a href=\"";
            // line 30
            echo $this->getAttribute($context["blog"], "href", array());
            echo "\">";
            echo (isset($context["text_read_more"]) ? $context["text_read_more"] : null);
            echo "</a> </div>
                                <div class=\"view-blog\">
                                    <div class=\"read-more\">
                                        <a href=\"";
            // line 33
            echo $this->getAttribute($context["blog"], "href", array());
            echo "\">
                                            <i class=\"fa fa-link\"></i>";
            // line 34
            echo (isset($context["text_read_more"]) ? $context["text_read_more"] : null);
            echo "
                                        </a>
                                    </div>
                                </div> -->
                        </div>
                    </div>
                ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['blog'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 41
        echo "                </div>
            </div>
        </div>
        <!--  <div class=\"buttons-see-all text-center\">
            <button type=\"button\" onclick=\"location='";
        // line 45
        echo (isset($context["all_blogs"]) ? $context["all_blogs"] : null);
        echo "';\" class=\"btn btn-primary\">";
        echo (isset($context["button_all_blogs"]) ? $context["button_all_blogs"] : null);
        echo "</button>
        </div> -->
    </div>


<script type=\"text/javascript\">
    \$(document).ready(function() {
    \$(\"#blog\").owlCarousel({
    itemsCustom : [
    [0, 1],
    [600, 2],
    [768, 2],
    [992, 2],
    [1200, 3],
    [1410, 3]
    ],
      // autoPlay: 1000,
      navigationText: ['<i class=\"fa fa-angle-left\" aria-hidden=\"true\"></i>', '<i class=\"fa fa-angle-right\" aria-hidden=\"true\"></i>'],
      navigation : true,
      pagination:false
    });
    });
</script>
";
    }

    public function getTemplateName()
    {
        return "techeco/template/extension/module/blogger.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  125 => 45,  119 => 41,  106 => 34,  102 => 33,  94 => 30,  90 => 29,  81 => 27,  76 => 25,  69 => 23,  66 => 22,  57 => 16,  53 => 15,  42 => 11,  38 => 9,  36 => 8,  33 => 7,  29 => 6,  22 => 2,  19 => 1,);
    }
}
/*     <div class="box blog_webi">*/
/*         <h1 class="heading">{{ heading_title }}</h1>*/
/*         <div class="box-content">*/
/*             <div class="box-product row">*/
/*                 <div id="blog" class="owl-carousel owl-theme">*/
/*                 {% for blog in blogs %}*/
/*                     <div class="product-block col-xs-12">*/
/*                         {% if blog.image %}*/
/*                             <div class="blog-left">*/
/*                                 <div class="webi-blog-image">*/
/*                                     <img src="{{ blog.image }}" alt="{{ heading_title }}" title="{{ heading_title }}" class="img-responsive" />*/
/*                                     <div class="blog-post-image-hover"> </div>*/
/*                                     <div class="webi_post_hover">*/
/*                                         <div class="blog-ic">*/
/*                                         <a class="icon zoom" title="Click to view Full Image " href="{{ blog.image }}" data-lightbox="example-set"><i class="fa fa-search"></i></a>*/
/*                                         <a class="icon readmore_link" title="all blog" href="{{ all_blogs }}"><i class="fa fa-link"></i></a>*/
/*                                     </div>*/
/*                                     </div>*/
/*                                 </div>*/
/*                             </div>*/
/*                         {% endif %}*/
/*                         <div class="blog-right">*/
/*                             <h4><a href="{{ blog.href }}">{{ blog.title }}</a> </h4>*/
/*                             <div class="webi-post-author">*/
/*                                 <i class="fa fa-calendar"></i><span class="date-time">{{ blog.date_added }}</span>*/
/*                                  <span class="seper">|</span>*/
/*                                  <span class="write-comment"> <a href="{{ blog.href }}">{{ blog.total_comments }} {{ entry_comment }}</a> </span>*/
/*                             </div>*/
/*                            <!--  <div class="blog-desc">{{ blog.description }}</div> -->*/
/*                             <!-- <div class="read_link"> <a href="{{ blog.href }}">{{ text_read_more }}</a> </div>*/
/*                                 <div class="view-blog">*/
/*                                     <div class="read-more">*/
/*                                         <a href="{{ blog.href }}">*/
/*                                             <i class="fa fa-link"></i>{{ text_read_more }}*/
/*                                         </a>*/
/*                                     </div>*/
/*                                 </div> -->*/
/*                         </div>*/
/*                     </div>*/
/*                 {% endfor %}*/
/*                 </div>*/
/*             </div>*/
/*         </div>*/
/*         <!--  <div class="buttons-see-all text-center">*/
/*             <button type="button" onclick="location='{{ all_blogs }}';" class="btn btn-primary">{{ button_all_blogs }}</button>*/
/*         </div> -->*/
/*     </div>*/
/* */
/* */
/* <script type="text/javascript">*/
/*     $(document).ready(function() {*/
/*     $("#blog").owlCarousel({*/
/*     itemsCustom : [*/
/*     [0, 1],*/
/*     [600, 2],*/
/*     [768, 2],*/
/*     [992, 2],*/
/*     [1200, 3],*/
/*     [1410, 3]*/
/*     ],*/
/*       // autoPlay: 1000,*/
/*       navigationText: ['<i class="fa fa-angle-left" aria-hidden="true"></i>', '<i class="fa fa-angle-right" aria-hidden="true"></i>'],*/
/*       navigation : true,*/
/*       pagination:false*/
/*     });*/
/*     });*/
/* </script>*/
/* */
