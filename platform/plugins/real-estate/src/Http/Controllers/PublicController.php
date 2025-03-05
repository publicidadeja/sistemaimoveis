<?php

namespace Srapid\RealEstate\Http\Controllers;

use Srapid\Base\Enums\BaseStatusEnum;
use Srapid\Base\Http\Responses\BaseHttpResponse;
use Srapid\Base\Supports\Helper;
use Srapid\RealEstate\Enums\ModerationStatusEnum;
use Srapid\RealEstate\Enums\PropertyStatusEnum;
use Srapid\RealEstate\Http\Requests\SendConsultRequest;
use Srapid\RealEstate\Models\Category;
use Srapid\RealEstate\Models\Consult;
use Srapid\RealEstate\Models\Project;
use Srapid\RealEstate\Models\Property;
use Srapid\RealEstate\Repositories\Interfaces\CategoryInterface;
use Srapid\RealEstate\Repositories\Interfaces\ConsultInterface;
use Srapid\RealEstate\Repositories\Interfaces\CurrencyInterface;
use Srapid\RealEstate\Repositories\Interfaces\ProjectInterface;
use Srapid\RealEstate\Repositories\Interfaces\PropertyInterface;
use Srapid\SeoHelper\SeoOpenGraph;
use Srapid\Slug\Repositories\Interfaces\SlugInterface;
use EmailHandler;
use Exception;
use File;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Mimey\MimeTypes;
use RealEstateHelper;
use RssFeed;
use RvMedia;
use SeoHelper;
use SlugHelper;
use Spatie\Feed\Feed;
use Spatie\Feed\FeedItem;
use Theme;
use Throwable;

class PublicController extends Controller
{

    /**
     * @param SendConsultRequest $request
     * @param BaseHttpResponse $response
     * @param ConsultInterface $consultRepository
     * @param PropertyInterface $propertyRepository
     * @param ProjectInterface $projectRepository
     * @return BaseHttpResponse
     * @throws Throwable
     */
    public function postSendConsult(
        SendConsultRequest $request,
        BaseHttpResponse $response,
        ConsultInterface $consultRepository,
        PropertyInterface $propertyRepository,
        ProjectInterface $projectRepository
    ) {
        try {
            /**
             * @var Consult $consult
             */
            $consult = $consultRepository->getModel();

            $sendTo = null;
            $link = null;
            $subject = null;

            if ($request->input('type') == 'project') {
                $request->merge(['project_id' => $request->input('data_id')]);
                $project = $projectRepository->findById($request->input('data_id'));
                if ($project) {
                    $link = $project->url;
                    $subject = $project->name;
                }
            } else {
                $request->merge(['property_id' => $request->input('data_id')]);
                $property = $propertyRepository->findById($request->input('data_id'), ['author']);
                if ($property) {
                    $link = $property->url;
                    $subject = $property->name;

                    if ($property->author->email) {
                        $sendTo = $property->author->email;
                    }
                }
            }

            $consult->fill($request->input());
            $consultRepository->createOrUpdate($consult);

            EmailHandler::setModule(REAL_ESTATE_MODULE_SCREEN_NAME)
                ->setVariableValues([
                    'consult_name'    => $consult->name ?? 'N/A',
                    'consult_email'   => $consult->email ?? 'N/A',
                    'consult_phone'   => $consult->phone ?? 'N/A',
                    'consult_content' => $consult->content ?? 'N/A',
                    'consult_link'    => $link ?? 'N/A',
                    'consult_subject' => $subject ?? 'N/A',
                ])
                ->sendUsingTemplate('notice', $sendTo);

            return $response->setMessage(trans('plugins/real-estate::consult.email.success'));
        } catch (Exception $exception) {
            info($exception->getMessage());
            return $response
                ->setError()
                ->setMessage(trans('plugins/real-estate::consult.email.failed'));
        }
    }

    /**
     * @param string $key
     * @param SlugInterface $slugRepository
     * @param ProjectInterface $projectRepository
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Response
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function getProject(string $key, SlugInterface $slugRepository, ProjectInterface $projectRepository)
    {
        $slug = $slugRepository->getFirstBy([
            'slugs.key'      => $key,
            'reference_type' => Project::class,
            'prefix'         => SlugHelper::getPrefix(Project::class),
        ]);

        if (!$slug) {
            abort(404);
        }

        $project = $projectRepository->getFirstBy(
            ['id' => $slug->reference_id],
            ['*'],
        );

        if (!$project) {
            abort(404);
        }

        $project->loadMissing(RealEstateHelper::getProjectRelationsQuery());

        if ($project->slugable->key !== $key) {
            return redirect()->to($project->url);
        }

        SeoHelper::setTitle($project->name)->setDescription(Str::words($project->description, 120));

        $meta = new SeoOpenGraph;
        if ($project->image) {
            $meta->setImage(RvMedia::getImageUrl($project->image));
        }
        $meta->setDescription($project->description);
        $meta->setUrl($project->url);
        $meta->setTitle($project->name);
        $meta->setType('article');

        SeoHelper::setSeoOpenGraph($meta);

        Theme::breadcrumb()
            ->add(__('Home'), url('/'))
            ->add($project->name, $project->url);

        $relatedProjects = $projectRepository->getRelatedProjects($project->id,
            theme_option('number_of_related_projects', 8));

        Theme::asset()->usePath()->add('validation-jquery-css',
            'libraries/jquery-validation/validationEngine.jquery.css');
        Theme::asset()->container('header')->usePath()->add('jquery-validationEngine-vi-js',
            'libraries/jquery-validation/jquery.validationEngine-vi.js', ['jquery']);
        Theme::asset()->container('header')->usePath()->add('jquery-validationEngine-js',
            'libraries/jquery-validation/jquery.validationEngine.js', ['jquery']);

        if (function_exists('admin_bar')) {
            admin_bar()->registerLink(__('Edit this project'), route('project.edit', $project->id));
        }

        Helper::handleViewCount($project, 'viewed_project');

        do_action(BASE_ACTION_PUBLIC_RENDER_SINGLE, PROJECT_MODULE_SCREEN_NAME, $project);

        $images = [];
        foreach ($project->images as $image) {
            $images[] = RvMedia::getImageUrl($image, null, false, RvMedia::getDefaultImage());
        }

        return Theme::scope('real-estate.project', compact('project', 'images', 'relatedProjects'))->render();
    }

    /**
     * @param string $key
     * @param SlugInterface $slugRepository
     * @param PropertyInterface $propertyRepository
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Response
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function getProperty(string $key, SlugInterface $slugRepository, PropertyInterface $propertyRepository)
    {
        $slug = $slugRepository->getFirstBy([
            'slugs.key'      => $key,
            'reference_type' => Property::class,
            'prefix'         => SlugHelper::getPrefix(Property::class),
        ]);

        if (!$slug) {
            abort(404);
        }

        $property = $propertyRepository->getProperty($slug->reference_id);

        if (!$property) {
            abort(404);
        }

        $property->loadMissing(RealEstateHelper::getPropertyRelationsQuery());

        if ($property->slugable->key !== $key) {
            return redirect()->to($property->url);
        }

        SeoHelper::setTitle($property->name)->setDescription(Str::words($property->description, 120));

        $meta = new SeoOpenGraph;
        if ($property->image) {
            $meta->setImage(RvMedia::getImageUrl($property->image));
        }
        $meta->setDescription($property->description);
        $meta->setUrl($property->url);
        $meta->setTitle($property->name);
        $meta->setType('article');

        SeoHelper::setSeoOpenGraph($meta);

        Theme::breadcrumb()
            ->add(__('Home'), route('public.index'))
            ->add($property->name, $property->url);

        Theme::asset()->usePath()->add('validation-jquery-css',
            'libraries/jquery-validation/validationEngine.jquery.css');
        Theme::asset()->container('header')->usePath()->add('jquery-validationEngine-vi-js',
            'libraries/jquery-validation/jquery.validationEngine-vi.js', ['jquery']);
        Theme::asset()->container('header')->usePath()->add('jquery-validationEngine-js',
            'libraries/jquery-validation/jquery.validationEngine.js', ['jquery']);

        Helper::handleViewCount($property, 'viewed_property');

        do_action(BASE_ACTION_PUBLIC_RENDER_SINGLE, PROPERTY_MODULE_SCREEN_NAME, $property);

        if (function_exists('admin_bar')) {
            admin_bar()->registerLink(__('Edit this property'), route('property.edit', $property->id));
        }

        $images = [];
        foreach ($property->images as $image) {
            $images[] = RvMedia::getImageUrl($image, null, false, RvMedia::getDefaultImage());
        }

        return Theme::scope('real-estate.property', compact('property', 'images'))->render();
    }

    /**
     * @param Request $request
     * @param ProjectInterface $projectRepository
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse|\Response
     */
    public function getProjects(
        Request $request,
        ProjectInterface $projectRepository,
        BaseHttpResponse $response
    ) {
        SeoHelper::setTitle(__('Projects'));

        $perPage = (int)$request->input('per_page') ? (int)$request->input('per_page') : (int)theme_option('number_of_projects_per_page',
            12);

        $filters = [
            'keyword'     => $request->input('k'),
            'blocks'      => $request->input('blocks'),
            'min_floor'   => $request->input('min_floor'),
            'max_floor'   => $request->input('max_floor'),
            'min_flat'    => $request->input('min_flat'),
            'max_flat'    => $request->input('max_flat'),
            'category_id' => $request->input('category_id'),
            'city_id'     => $request->input('city_id'),
            'location'    => $request->input('location'),
            'sort_by'     => $request->input('sort_by'),
        ];

        $params = [
            'paginate' => [
                'per_page'      => $perPage ?: 12,
                'current_paged' => (int)$request->input('page', 1),
            ],
            'order_by' => ['re_projects.created_at' => 'DESC'],
            'with'     => RealEstateHelper::getProjectRelationsQuery(),
        ];

        $projects = $projectRepository->getProjects($filters, $params);

        if ($request->ajax()) {
            if ($request->input('minimal')) {
                return $response->setData(Theme::partial('search-suggestion', ['items' => $projects]));
            }

            return $response->setData(Theme::partial('real-estate.projects.items', ['projects' => $projects]));
        }

        Theme::breadcrumb()
            ->add(__('Home'), route('public.index'))
            ->add(__('Projects'), route('public.projects'));

        $categories = get_property_categories([
            'indent'     => '↳',
            'conditions' => ['status' => BaseStatusEnum::PUBLISHED],
        ]);

        return Theme::scope('real-estate.projects', compact('projects', 'categories'))->render();
    }

    /**
     * @param Request $request
     * @param PropertyInterface $propertyRepository
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse|\Response
     */
    public function getProperties(
        Request $request,
        PropertyInterface $propertyRepository,
        BaseHttpResponse $response
    ) {
        SeoHelper::setTitle(__('Properties'));

        $perPage = (int)$request->input('per_page') ? (int)$request->input('per_page') : (int)theme_option('number_of_properties_per_page',
            12);

        $filters = [
            'keyword'     => $request->input('k'),
            'type'        => $request->input('type'),
            'bedroom'     => $request->input('bedroom'),
            'bathroom'    => $request->input('bathroom'),
            'floor'       => $request->input('floor'),
            'min_price'   => $request->input('min_price'),
            'max_price'   => $request->input('max_price'),
            'min_square'  => $request->input('min_square'),
            'max_square'  => $request->input('max_square'),
            'project'     => $request->input('project'),
            'category_id' => $request->input('category_id'),
            'city'        => $request->input('city'),
            'city_id'     => $request->input('city_id'),
            'location'    => $request->input('location'),
            'sort_by'     => $request->input('sort_by'),
        ];

        $params = [
            'paginate' => [
                'per_page'      => $perPage ?: 12,
                'current_paged' => (int)$request->input('page', 1),
            ],
            'order_by' => ['re_properties.created_at' => 'DESC'],
            'with'     => RealEstateHelper::getPropertyRelationsQuery(),
        ];

        $properties = $propertyRepository->getProperties($filters, $params);

        if ($request->ajax()) {
            if ($request->input('minimal')) {
                return $response->setData(Theme::partial('search-suggestion', ['items' => $properties]));
            }

            return $response->setData(Theme::partial('real-estate.properties.items', ['properties' => $properties]));
        }

        Theme::breadcrumb()
            ->add(__('Home'), route('public.index'))
            ->add(__('Properties'), route('public.properties'));

        $categories = get_property_categories([
            'indent'     => '↳',
            'conditions' => ['status' => BaseStatusEnum::PUBLISHED],
        ]);

        return Theme::scope('real-estate.properties', compact('properties', 'categories'))->render();
    }

    /**
     * @param string $key
     * @param Request $request
     * @param SlugInterface $slugRepository
     * @param PropertyInterface $propertyRepository
     * @param CategoryInterface $categoryRepository
     * @return \Response
     */
    public function getPropertyCategory(
        $key,
        Request $request,
        SlugInterface $slugRepository,
        PropertyInterface $propertyRepository,
        CategoryInterface $categoryRepository
    ) {
        $slug = $slugRepository->getFirstBy([
            'slugs.key'      => $key,
            'reference_type' => Category::class,
            'prefix'         => SlugHelper::getPrefix(Category::class),
        ]);

        if (!$slug) {
            abort(404);
        }

        $category = $categoryRepository->getFirstBy(
            ['id' => $slug->reference_id],
            ['*'],
            ['slugable']
        );

        if (!$category) {
            abort(404);
        }

        SeoHelper::setTitle($category->name)->setDescription(Str::words($category->description, 120));

        $meta = new SeoOpenGraph;
        $meta->setDescription($category->description);
        $meta->setUrl($category->url);
        $meta->setTitle($category->name);
        $meta->setType('article');

        SeoHelper::setSeoOpenGraph($meta);

        Theme::breadcrumb()
            ->add(__('Home'), route('public.index'))
            ->add($category->name, $category->url);

        $filters = [
            'category_id' => $category->id,
        ];

        $perPage = (int)theme_option('number_of_properties_per_page', 12);

        $params = [
            'paginate' => [
                'per_page'      => $perPage ?: 12,
                'current_paged' => (int)$request->input('page', 1),
            ],
            'order_by' => ['re_properties.created_at' => 'DESC'],
            'with'     => RealEstateHelper::getPropertyRelationsQuery(),
        ];

        $properties = $propertyRepository->getProperties($filters, $params);

        return Theme::scope('real-estate.property-category', compact('category', 'properties'))->render();
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @param null $title
     * @param CurrencyInterface $currencyRepository
     * @return BaseHttpResponse
     */
    public function changeCurrency(
        Request $request,
        BaseHttpResponse $response,
        CurrencyInterface $currencyRepository,
        $title = null
    ) {
        if (empty($title)) {
            $title = $request->input('currency');
        }

        if (!$title) {
            return $response;
        }

        $currency = $currencyRepository->getFirstBy(['title' => $title]);

        if ($currency) {
            cms_currency()->setApplicationCurrency($currency);
        }

        return $response;
    }

    /**
     * @param PropertyInterface $propertyRepository
     * @return Feed
     */
    public function getPropertyFeeds(PropertyInterface $propertyRepository)
    {
        if (!is_plugin_active('rss-feed')) {
            abort(404);
        }

        $data = $propertyRepository->getProperties([], [
            'take' => 20,
            'with' => ['slugable', 'categories', 'author'],
        ]);

        $feedItems = collect([]);

        foreach ($data as $item) {
            $imageURL = RvMedia::getImageUrl($item->image, null, false, RvMedia::getDefaultImage());

            $feedItems[] = FeedItem::create()
                ->id($item->id)
                ->title(clean($item->name))
                ->summary(clean($item->description))
                ->updated($item->updated_at)
                ->enclosure($imageURL)
                ->enclosureType((new MimeTypes)->getMimeType(File::extension($imageURL)))
                ->enclosureLength(RssFeed::remoteFilesize($imageURL))
                ->category((string)$item->category->name)
                ->link((string)$item->url)
                ->author($item->author_id ? $item->author->name : '');
        }

        return RssFeed::renderFeedItems($feedItems, 'Properties feed',
            'Latest properties from ' . theme_option('site_title'));
    }
    
    /**
     * Gera um feed XML para integração com ZAP Imóveis
     * 
     * @return Response
     */
    public function getZapImoveisXml()
    {
        $properties = Property::where('moderation_status', ModerationStatusEnum::APPROVED)
            ->where('status', PropertyStatusEnum::SELLING)
            ->with(['features', 'facilities', 'city', 'city.state', 'currency', 'categories'])
            ->get();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>
        <ListingDataFeed xmlns="http://www.zapimoveis.com.br/XMLSchema"
          xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
          xsi:schemaLocation="http://www.zapimoveis.com.br/XMLSchema https://www.zapimoveis.com.br/xml/listing.xsd">
          <Header>
            <Provider>' . setting('theme_name', config('app.name')) . '</Provider>
            <Email>' . setting('admin_email', 'admin@example.com') . '</Email>
            <ContactInfo>' . setting('admin_phone', '(11) 99999-9999') . '</ContactInfo>
            <LastUpdate>' . now()->format('Y-m-d\TH:i:s') . '</LastUpdate>
          </Header>
          <Listings>';

        foreach ($properties as $property) {
            // Define o tipo do imóvel com base na categoria
            $propertyType = $this->mapPropertyType($property->category_name ?? '');
            
            // Define tipo da transação (venda ou aluguel)
            $listingType = strtolower($property->type) === 'rent' ? 'RENTAL' : 'SALE';
            
            // Formata preço removendo formatação
            $price = str_replace(['R$', '.', ' '], '', $property->price);
            $price = str_replace(',', '.', $price);
            
            // Ajusta imagens
            $images = '';
            foreach ($property->images as $image) {
                if (!empty($image)) {
                    $fullImageUrl = url($image);
                    $images .= "<Image><ImageUrl>{$fullImageUrl}</ImageUrl></Image>";
                }
            }
            
            // Monta as features
            $features = '';
            if ($property->features && $property->features->count() > 0) {
                foreach ($property->features as $feature) {
                    $features .= "<Feature>{$feature->name}</Feature>";
                }
            }
            
            // Descrição sem tags HTML
            $description = strip_tags($property->content);
            
            $xml .= "
            <Listing>
              <ListingID>{$property->id}</ListingID>
              <Title>" . htmlspecialchars($property->name, ENT_XML1) . "</Title>
              <PropertyType>{$propertyType}</PropertyType>
              <TransactionType>{$listingType}</TransactionType>
              <ListPrice currency=\"BRL\">{$price}</ListPrice>
              <Description>" . htmlspecialchars($description, ENT_XML1) . "</Description>
              <Location>
                <Address>" . htmlspecialchars($property->location, ENT_XML1) . "</Address>
                <City>" . htmlspecialchars($property->city->name ?? '', ENT_XML1) . "</City>
                <State>" . htmlspecialchars($property->city->state->name ?? '', ENT_XML1) . "</State>
              </Location>
              <Details>
                <Bedrooms>{$property->number_bedroom}</Bedrooms>
                <Bathrooms>{$property->number_bathroom}</Bathrooms>
                <ParkingSpaces>{$property->number_garage}</ParkingSpaces>
                <UsableArea unit=\"SQM\">{$property->square}</UsableArea>
                <TotalArea unit=\"SQM\">{$property->square}</TotalArea>
                {$features}
              </Details>
              <Media>
                {$images}
              </Media>
              <ContactInfo>
                <Name>" . setting('theme_name', config('app.name')) . "</Name>
                <Email>" . setting('admin_email', 'admin@example.com') . "</Email>
                <Phone>" . setting('admin_phone', '(11) 99999-9999') . "</Phone>
                <Website>" . url('/') . "</Website>
              </ContactInfo>
            </Listing>";
        }

        $xml .= '
          </Listings>
        </ListingDataFeed>';

        return response($xml, 200, [
            'Content-Type' => 'application/xml',
            'Cache-Control' => 'no-cache, must-revalidate',
        ]);
    }

    /**
     * Mapeia categorias do sistema para os tipos de propriedade aceitos pelo ZAP Imóveis
     * 
     * @param string $categoryName
     * @return string
     */
    protected function mapPropertyType($categoryName)
    {
        $categoryName = strtolower($categoryName);
        
        $map = [
            'apartamento' => 'APARTMENT',
            'casa' => 'HOUSE',
            'terreno' => 'LAND',
            'comercial' => 'COMMERCIAL',
            'sala' => 'OFFICE',
            'loja' => 'STORE',
            'galpão' => 'WAREHOUSE',
            'rural' => 'FARM',
            'hotel' => 'HOTEL',
        ];
        
        foreach ($map as $key => $value) {
            if (strpos($categoryName, $key) !== false) {
                return $value;
            }
        }
        
        // Padrão caso não encontre correspondência
        return 'RESIDENTIAL';
    }
}
