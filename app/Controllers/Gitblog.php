<?php

namespace App\Controllers;

use App\Libraries\Markdown;
use App\Libraries\Pager;
use App\Libraries\Twig;
use App\Libraries\Yaml;

class Gitblog extends BaseController
{

    //配置信息
    private $confObj;

    //渲染时的数据绑定
    private $data;

    //是否导出操作
    private $export = false;

    //配置文件
    private $configFile;

    //博客路径
    private $blogPath;
    private $markdown;
    /**
     * @var Pager
     */
    private $pager;
    /**
     * @var Yaml
     */
    private $yaml;
    private $twig;

    function __construct()
    {
        $this->init();

    }

    //导出网站
    public function exportSite()
    {
        $this->export = true;

        //初始化
        $this->init();

        echo "\nexport index page\n";

        //首页所有页面
        $pageSize = $this->confObj['blog']['pageSize'];
        $pages = $this->markdown->getTotalPages($pageSize);
        for ($pageNo = 1; $pageNo <= $pages; $pageNo++) {
            $fileContent = $this->page($pageNo);
            $filePath = GB_SITE_DIR . "/page/";
            if (!file_exists($filePath)) {
                mkdir($filePath, 0755, true);
            }
            // var_dump($pageNo,$fileContent);exit;
            write_file($filePath . $pageNo . ".html", $fileContent);

            if ($pageNo == 1) {
                if (!file_exists($filePath)) {
                    mkdir($filePath, 0755, true);
                }
                write_file(GB_SITE_DIR . "/index.html", $fileContent);
            }
        }
        echo "export index page success\n";

        echo "\nexport category page\n";
        //分类下所有页面
        $categoryList = $this->markdown->getAllCategorys();
        foreach ($categoryList as $idx => $category) {
            $categoryId = $category['name'];
            $pages = $this->markdown->getCategoryTotalPages($categoryId, $pageSize);

            for ($pageNo = 1; $pageNo <= $pages; $pageNo++) {
                $fileContent = $this->category($categoryId, $pageNo);
                $filePath = GB_SITE_DIR . "/category/" . urlencode($categoryId) . "/page/";
                if (!file_exists($filePath)) {
                    mkdir($filePath, 0755, true);
                }
                write_file($filePath . $pageNo . ".html", $fileContent);
                if ($pageNo == 1) {
                    $filePath = GB_SITE_DIR . "/category/" . urlencode($categoryId);
                    if (!file_exists($filePath)) {
                        mkdir($filePath, 0755, true);
                    }
                    write_file($filePath . ".html", $fileContent);
                }
            }
        }
        echo "export category page success\n";

        echo "\nexport tag page\n";
        //标签下所有页面
        $tagsList = $this->markdown->getAllTags();
        foreach ($tagsList as $idx => $tag) {
            $tagId = $tag['name'];
            $pages = $this->markdown->getTagTotalPages($tagId, $pageSize);

            for ($pageNo = 1; $pageNo <= $pages; $pageNo++) {
                $fileContent = $this->tags($tagId, $pageNo);
                $filePath = GB_SITE_DIR . "/tags/" . urlencode($tagId) . "/page/";
                if (!file_exists($filePath)) {
                    mkdir($filePath, 0755, true);
                }
                write_file($filePath . $pageNo . ".html", $fileContent);
                if ($pageNo == 1) {
                    $filePath = GB_SITE_DIR . "/tags/" . urlencode($tagId);
                    if (!file_exists($filePath)) {
                        mkdir($filePath, 0755, true);
                    }
                    write_file($filePath . ".html", $fileContent);
                }
            }
        }
        echo "export tag page success\n";

        echo "\nexport archive page\n";
        //归档下所有页面
        $yearMonthList = $this->markdown->getAllYearMonths();
        foreach ($yearMonthList as $idx => $yearMonth) {
            $yearMonthId = $yearMonth['id'];
            $pages = $this->markdown->getYearMonthTotalPages($yearMonthId, $pageSize);

            for ($pageNo = 1; $pageNo <= $pages; $pageNo++) {
                $fileContent = $this->archive($yearMonthId, $pageNo);
                $filePath = GB_SITE_DIR . "/archive/$yearMonthId/page/";
                if (!file_exists($filePath)) {
                    mkdir($filePath, 0755, true);
                }
                write_file($filePath . $pageNo . ".html", $fileContent);
                if ($pageNo == 1) {
                    $filePath = GB_SITE_DIR . "/archive/$yearMonthId";
                    if (!file_exists($filePath)) {
                        mkdir($filePath, 0755, true);
                    }
                    write_file($filePath . ".html", $fileContent);
                }
            }
        }
        echo "\nexport archive page success\n";

        echo "\nexport detail page\n";
        //博客详情页
        $blogList = $this->markdown->getAllBlogs();
        foreach ($blogList as $idx => $blog) {
            $blogId = $blog['blogId'];
            $siteURL = substr($blog['siteURL'], strlen($this->confObj['url']));
            $fileName = $blog['fileName'];
            $fileName = $this->markdown->changeFileExt($fileName);
            $filePath = str_replace($fileName, "", $siteURL);
            $filePath = GB_SITE_DIR . '/' . $filePath;
            if (!file_exists($filePath)) {
                mkdir($filePath, 0755, true);
            }

            $fileContent = $this->blog($blogId);
            write_file(GB_SITE_DIR . '/' . $siteURL, $fileContent);
        }
        echo "export detail page success\n";

        echo "\ncopy theme\n";
        //复制主题到目录下
        $theme = $this->confObj['theme'];
        $thfiles = get_dir_file_info("./theme/" . $theme, false);
        foreach ($thfiles as $fileName => $file) {
            $pics = explode('.', $fileName);
            $fileExt = strtolower(end($pics));

            if ($fileExt != "html") {
                $serverPath = $file['server_path'];
                $serverPath = str_replace("\\", "/", $serverPath);

                $targetFile = GB_SITE_DIR . substr($serverPath, strpos($serverPath, "/theme/" . $theme));
                $targetPath = str_replace(basename($fileName), "", $targetFile);

                if (!file_exists($targetPath)) {
                    mkdir($targetPath, 0755, true);
                }

                copy($serverPath, $targetFile);
            }
        }
        echo "copy theme success\n";

        echo "\ncopy image\n";
        //复制图片文件夹
        $thfiles = get_dir_file_info("./blog/img/", false);
        foreach ($thfiles as $fileName => $file) {
            $serverPath = $file['server_path'];
            $serverPath = str_replace("\\", "/", $serverPath);

            $targetPath = GB_SITE_DIR . "/blog/img/";
            $targetFile = GB_SITE_DIR . "/blog/img/" . $file['name'];
            if (!file_exists($targetPath)) {
                mkdir($targetPath, 0755, true);
            }
            copy($serverPath, $targetFile);
        }
        echo "copy image success\n";

        echo "\ncopy feed.xml, robots.txt, favicon.ico\n";
        //feed.xml文件
        $feedXmlfilePath = GB_SITE_DIR . "/feed.xml";
        $feedXmlfileContent = $this->feed();
        write_file($feedXmlfilePath, $feedXmlfileContent);

        copy("robots.txt", GB_SITE_DIR . "/robots.txt");
        copy("favicon.ico", GB_SITE_DIR . "/favicon.ico");

        echo "\nexport site success!!!\n";
        return true;
    }

    //首页
    public function index()
    {
        $this->page(1);
    }

    private function init()
    {
        helper("url");
        $this->configFile = str_replace("\\", "/", dirname(APPPATH)) . '/' . GB_CONF_FILE;
        $this->blogPath = str_replace("\\", "/", dirname(APPPATH)) . '/blog/';
        //加载必要的类库
        $this->yaml = new Yaml();
        $this->markdown = new Markdown();
        $this->pager = new Pager();

        //加载配置文件
        $this->confObj = $this->yaml->getConfObject($this->configFile);

        $this->twig = new Twig($this->confObj['theme']);
        //自动检测url配置
        //$this->confObj['url'] =base_url()."/";


        //侧边栏最近博客条数
        $recentSize = $this->confObj['blog']['recentSize'];

        //是否需要所有博客
        $allBlogsForPage = $this->confObj['blog']['allBlogsForPage'];


        //初始化博客信息
        $this->markdown->initAllBlogData($this->blogPath, $this->confObj);

        //所有博客
        $allBlogsList = null;
        if ($allBlogsForPage) {
            //所有博客
            $allBlogsList = $this->markdown->getAllBlogs();
        }

        //所有分类
        $categoryList = $this->markdown->getAllCategorys();

        //所有标签
        $tagsList = $this->markdown->getAllTags();

        //归档月份
        $yearMonthList = $this->markdown->getAllYearMonths();

        //最近博客
        $recentBlogList = $this->markdown->getBlogsRecent($recentSize);

        //设置数据
        $this->setData("allBlogsList", $allBlogsList);
        $this->setData("categoryList", $categoryList);
        $this->setData("tagsList", $tagsList);
        $this->setData("yearMonthList", $yearMonthList);
        $this->setData("recentBlogList", $recentBlogList);
        $this->setData("confObj", $this->confObj);

        //配置名件对象别名
        $this->setData("site", $this->confObj);
    }

    //加载缓存文件
    private function loadOutCache()
    {
        $flag = false;
        $cacheKey = $this->getCacheKey();
        $html = cache()->get($cacheKey);
        if (!$html) { //没有缓存
            $this->init();
        } else {
            $this->response->setBody($html);
            $cacheHeaderVal = strtoupper(substr($cacheKey, 0, 32));
            $this->response->setHeader("Cache-Key", $cacheHeaderVal);
            $flag = true;
        }

        return $flag;
    }

    //分类下的博客列表
    public function category($categoryId, $pageNo = 1)
    {
        if ($this->loadOutCache()) {
            return true;
        }

        //$categoryId = (int)$categoryId;
        $categoryId = urldecode($categoryId);
        $pageNo = (int)$pageNo;
        $pageSize = $this->confObj['blog']['pageSize'];
        $pageBarSize = $this->confObj['blog']['pageBarSize'];

        $pages = $this->markdown->getCategoryTotalPages($categoryId, $pageSize);

        if ($pageNo <= 0) {
            $pageNo = 1;
        }

        if ($pageNo > $pages) {
            $pageNo = $pages;
        }

        $category = $this->markdown->getCategoryById($categoryId);
        $pageData = $this->markdown->getBlogsPageByCategory($categoryId, $pageNo, $pageSize);
        $pagination = $this->pager->splitPage($pages, $pageNo, $pageBarSize, $this->confObj['url'] . "category/$categoryId/");

        $this->setData("pagination", $pagination);
        $this->setData("pageName", "category");
        $this->setData("categoryId", $categoryId);
        $this->setData("category", $category);
        $this->setData("pageNo", $pageNo);
        $this->setData("pages", $pageData['pages']);
        $this->setData("blogList", $pageData['blogList']);

        return $this->render('index.html');
    }

    //按月归档下的博客列表
    public function archive($yearMonthId, $pageNo = 1)
    {
        if ($this->loadOutCache()) {
            return $this->response->send();
        }

        $pageNo = (int)$pageNo;
        $pageSize = $this->confObj['blog']['pageSize'];
        $pageBarSize = $this->confObj['blog']['pageBarSize'];

        $pages = $this->markdown->getYearMonthTotalPages($yearMonthId, $pageSize);

        if ($pageNo <= 0) {
            $pageNo = 1;
        }

        if ($pageNo > $pages) {
            $pageNo = $pages;
        }

        $yearMonth = $this->markdown->getYearMonthById($yearMonthId);
        $pageData = $this->markdown->getBlogsPageByYearMonth($yearMonthId, $pageNo, $pageSize);
        $pagination = $this->pager->splitPage($pages, $pageNo, $pageBarSize, $this->confObj['url'] . "archive/$yearMonthId/");

        $this->setData("pagination", $pagination);
        $this->setData("pageName", "archive");
        $this->setData("yearMonthId", $yearMonthId);
        $this->setData("yearMonth", $yearMonth);
        $this->setData("pageNo", $pageNo);
        $this->setData("pages", $pageData['pages']);
        $this->setData("blogList", $pageData['blogList']);

        return $this->render('index.html');
    }

    //标签下的博客列表
    public function tags($tagId, $pageNo = 1)
    {
        if ($this->loadOutCache()) {
            return $this->response->send();
        }

        $this->pageName = "tags";

        //$tagId = (int)$tagId;
        $tagId = urldecode($tagId);
        $pageNo = (int)$pageNo;
        $pageSize = $this->confObj['blog']['pageSize'];
        $pageBarSize = $this->confObj['blog']['pageBarSize'];

        $pages = $this->markdown->getTagTotalPages($tagId, $pageSize);

        if ($pageNo <= 0) {
            $pageNo = 1;
        }

        if ($pageNo > $pages) {
            $pageNo = $pages;
        }

        $tag = $this->markdown->getTagById($tagId);
        $pageData = $this->markdown->getBlogsPageByTag($tagId, $pageNo, $pageSize);
        $pagination = $this->pager->splitPage($pages, $pageNo, $pageBarSize, $this->confObj['url'] . "tags/$tagId/");

        $this->setData("pagination", $pagination);
        $this->setData("pageName", "tags");
        $this->setData("tagId", $tagId);
        $this->setData("tag", $tag);
        $this->setData("pageNo", $pageNo);
        $this->setData("pages", $pageData['pages']);
        $this->setData("blogList", $pageData['blogList']);

        return $this->render('index.html');
    }

    //首页，博客列表
    public function page($pageNo = 1)
    {
        if (!$this->export && $this->loadOutCache()) {
            return $this->response->send();
        }
        $pageNo = (int)$pageNo;
        $pageSize = $this->confObj['blog']['pageSize'];
        $pageBarSize = $this->confObj['blog']['pageBarSize'];

        $pages = $this->markdown->getTotalPages($pageSize);
        if ($pageNo <= 0) {
            $pageNo = 1;
        }

        if ($pageNo > $pages) {
            $pageNo = $pages;
        }
        $pageData = $this->markdown->getBlogsByPage($pageNo, $pageSize);

        $pagination = $this->pager->splitPage($pages, $pageNo, $pageBarSize, $this->confObj['url']);
        $this->setData("pagination", $pagination);

        $this->setData("pageName", "home");
        $this->setData("pageNo", $pageNo);
        $this->setData("pages", $pageData['pages']);
        $this->setData("blogList", $pageData['blogList']);

        return $this->render('index.html');
    }

    public function search()
    {
        $keyword = $this->request->getGet("keyword");
        $keyword = trim($keyword);
        $blogList = array();

        if (!empty($keyword)) {
            $blogList = $this->markdown->getBlogByKeyword($keyword);
        }

        $this->setData("pageName", "search");
        $this->setData("keyword", $keyword);
        $this->setData("blogList", $blogList);

        return $this->render('index.html');
    }

    //博客详情页
    public function blog($blogId = null)
    {
        if ($this->loadOutCache()) {
            return $this->response->send();
        }

        if (!$blogId) {
            $blogId = md5(uri_string());
        }

        $blog = $this->markdown->getBlogById($blogId);

        if ($blog == null) {
            return $this->go404();
        }

        $this->setData("pageName", "blog");
        $this->setData("blog", $blog);

        return $this->render('detail.html');
    }

    //feed.xml
    public function feed()
    {

        //加载必要的类库
        //初始化博客信息
        $this->markdown->initAllBlogData($this->blogPath, $this->confObj);

        $blogList = $this->markdown->getAllBlogs();

        $data['blogList'] = $blogList;
        $data['site'] = $this->yaml->getConfObject($this->configFile);

        $feedXml = view('feed', $data, []);
        if (!$this->export) {
            cache()->save('feed.xml', $feedXml, 24 * 60 * 60);
            $this->response->setContentType("application/xml");
            $this->response->setBody($feedXml);
        }

        return $feedXml;
    }

    public function go404()
    {
        $this->response->setStatusCode(404);
        $this->render("404.html");
    }

    //设置渲染数据
    private function setData($key, $dataObj)
    {
        $this->data[$key] = $dataObj;
    }

    //渲染页面
    private function render($tpl)
    {
        $htmlPage = $this->twig->render($tpl, $this->data);

        if (!$this->export) {
            //不是cli导出
            //是否使用缓存呢
            if ($this->confObj['enableCache']) {
                $cacheKey = $this->getCacheKey();
                cache()->save($cacheKey, $htmlPage, GB_PAGE_CACHE_TIME);
            }
            $this->response->setBody($htmlPage);
            return $this->response->send();
        } else {
            //是cli导出
            return $htmlPage;

        }

    }

    //计算缓存Key
    private function getCacheKey()
    {
        return $this->confObj['theme'] . "_" . md5(uri_string()) . ".html"; //category/1460001917
    }
}
