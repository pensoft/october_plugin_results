<?php namespace Pensoft\Results\Updates;

use October\Rain\Database\Updates\Seeder;
use Pensoft\Results\Models\Category;
use Pensoft\Results\Models\Phase;
use Pensoft\Results\Models\Result;

/**
 * Seeds the "What CONCERTO Achieved" timeline described in CON-86.
 *
 * Node titles, month labels and positions are taken from the approved design,
 * the audience assignments from the category list in the ticket, and the pop-up
 * copy from the editorial document linked in the ticket.
 */
class SeedResultsData extends Seeder
{
    public function run()
    {
        $categories = $this->seedCategories();
        $phases = $this->seedPhases();

        foreach ($this->results() as $sort => $data) {
            if (Result::where('slug', $data['slug'])->exists()) {
                continue;
            }

            $result = new Result;
            $result->title = $data['title'];
            $result->slug = $data['slug'];
            $result->month_label = $data['month_label'];
            $result->description = $data['description'];
            $result->relevant_for = $data['relevant_for'];
            $result->how_used = $data['how_used'];
            $result->materials = $this->materials($data['materials']);
            $result->position = $data['position'];
            $result->position_x = $data['position_x'];
            // offset_y is deliberately not set here: this seeder runs at 1.0.3,
            // before the column is added at 1.0.6. BackfillResultOffsets applies
            // the values from results() once the column exists.
            $result->phase_id = $phases[$data['phase']] ?? null;
            $result->sort_order = $sort + 1;
            $result->is_published = true;
            $result->save();

            $ids = [];
            foreach ($data['categories'] as $categorySlug) {
                if (isset($categories[$categorySlug])) {
                    $ids[] = $categories[$categorySlug];
                }
            }

            if ($ids) {
                $result->categories()->sync($ids);
            }
        }
    }

    /**
     * @return array slug => id
     */
    protected function seedCategories()
    {
        $names = [
            'researchers' => 'Researchers',
            'climate-modellers' => 'Climate modellers',
            'earth-observation-community' => 'Earth observation community',
            'policymakers' => 'Policymakers',
            'climate-services' => 'Climate services',
        ];

        $map = [];
        $sort = 1;

        foreach ($names as $slug => $name) {
            $category = Category::where('slug', $slug)->first();

            if (!$category) {
                $category = new Category;
                $category->name = $name;
                $category->slug = $slug;
                $category->is_visible = true;
                $category->sort_order = $sort;
                $category->save();
            }

            $map[$slug] = $category->id;
            $sort++;
        }

        return $map;
    }

    /**
     * @return array slug => id
     */
    protected function seedPhases()
    {
        $phases = [
            'data-foundation' => ['Data Foundation', 13.33],
            'new-earth-observation' => ['New Earth Observation', 37.78],
            'model-integration' => ['Model Integration', 62.22],
            'climate-demonstration' => ['Climate Demonstration', 86.67],
        ];

        $map = [];
        $sort = 1;

        foreach ($phases as $slug => $data) {
            $phase = Phase::where('slug', $slug)->first();

            if (!$phase) {
                $phase = new Phase;
                $phase->name = $data[0];
                $phase->slug = $slug;
                $phase->position_x = $data[1];
                $phase->sort_order = $sort;
                $phase->save();
            }

            $map[$slug] = $phase->id;
            $sort++;
        }

        return $map;
    }

    /**
     * Turns the list of available materials into the markup the pop-up expects.
     */
    protected function materials(array $items)
    {
        $markup = '<ul>';

        foreach ($items as $item) {
            $markup .= '<li>' . $item . '</li>';
        }

        return $markup . '</ul>';
    }

    protected function results()
    {
        return [
            [
                'title' => 'Land-use Change Dataset',
                'slug' => 'land-use-change-dataset',
                'month_label' => 'M12',
                'position' => 'above',
                'position_x' => 20.00,
                'offset_y' => 84,
                'phase' => 'data-foundation',
                'categories' => ['earth-observation-community'],
                'description' => 'A high-resolution global dataset showing how land use has changed since 1850 and how it may continue to change under different future climate scenarios up to 2100. It will provide more detailed information for studying the relationship between land-use change, the carbon cycle and the climate.',
                'relevant_for' => '<p>Climate and Earth system modellers, researchers studying land-use and environmental change, public authorities, policymakers and organisations working with climate services.</p>',
                'how_used' => '<p>The dataset can support climate simulations, historical analyses and future projections. It can also help assess how changes in forests, agricultural areas and other land uses affect the carbon cycle and the climate.</p>',
                'materials' => ['Deliverables'],
            ],
            [
                'title' => 'CONCERTO Data Sharing Platform',
                'slug' => 'concerto-data-sharing-platform',
                'month_label' => 'M12-M32',
                'position' => 'below',
                'position_x' => 29.00,
                'offset_y' => 60,
                'phase' => 'data-foundation',
                'categories' => [
                    'researchers',
                    'climate-modellers',
                    'earth-observation-community',
                    'policymakers',
                    'climate-services',
                ],
                'description' => 'A shared online platform bringing together the satellite observations, field measurements and modelling data used and produced by CONCERTO. It will help project partners and other users discover, access and reuse relevant data while reducing duplication and supporting open and consistent data management.',
                'relevant_for' => '<p>CONCERTO partners, researchers, data specialists, Earth observation experts, climate modellers and other projects working on land and carbon-cycle research.</p>',
                'how_used' => '<p>Users can search for and access relevant datasets through a common environment. The platform can support research, model development, data comparison and the reuse of CONCERTO data beyond the project.</p>',
                'materials' => ['Deliverables', 'The platform itself'],
            ],
            [
                'title' => 'Top-down Isoprene Flux Datasets',
                'slug' => 'top-down-isoprene-flux-datasets',
                'month_label' => 'M18-M48',
                'position' => 'above',
                'position_x' => 29.20,
                'offset_y' => 38,
                'phase' => 'data-foundation',
                'categories' => [],
                'description' => 'New global datasets estimating the amount of isoprene released by vegetation into the atmosphere. Based on satellite observations and atmospheric modelling, the datasets will improve understanding of how vegetation emissions interact with atmospheric processes and respond to environmental change.',
                'relevant_for' => '<p>Atmospheric scientists, climate researchers, vegetation and carbon-cycle experts, air-quality modellers and Earth observation specialists.</p>',
                'how_used' => '<p>The datasets can help researchers study how vegetation emissions interact with the atmosphere and respond to environmental change. They can also be used to evaluate and improve atmospheric, vegetation and climate models.</p>',
                'materials' => ['Deliverables', 'Datasets'],
            ],
            [
                'title' => 'Global land-cover and Leaf Area Index Datasets',
                'slug' => 'global-land-cover-and-leaf-area-index-datasets',
                'month_label' => 'M24',
                'position' => 'below',
                'position_x' => 45.60,
                'offset_y' => 60,
                'phase' => 'new-earth-observation',
                'categories' => ['climate-modellers', 'climate-services'],
                'description' => 'New global datasets showing changes in land cover and vegetation density over time. Produced using satellite observations and machine-learning methods, they will help climate models represent vegetation development more accurately and assess how ecosystems respond to changing environmental conditions.',
                'relevant_for' => '<p>Climate and Earth system modellers, Earth observation specialists, ecologists, land-use researchers and organisations monitoring vegetation and ecosystem change.</p>',
                'how_used' => '<p>The datasets can be incorporated into land and climate models to represent vegetation development more accurately. They can also support the monitoring and analysis of changes in land cover, vegetation growth and ecosystem conditions.</p>',
                'materials' => ['Deliverables'],
            ],
            [
                'title' => 'Vegetation Observation Operators',
                'slug' => 'vegetation-observation-operators',
                'month_label' => 'M24',
                'position' => 'above',
                'position_x' => 50.00,
                'offset_y' => 38,
                'phase' => 'new-earth-observation',
                'categories' => ['earth-observation-community'],
                'description' => 'New methods for translating satellite observations of vegetation into information that can be used by land and climate models. They will help connect what satellites observe from space with the vegetation and carbon-cycle processes represented within Earth system models.',
                'relevant_for' => '<p>Climate modellers, data-assimilation specialists, Earth observation researchers, satellite-mission teams and scientists working on vegetation and the carbon cycle.</p>',
                'how_used' => '<p>The methods can help models make better use of satellite information on vegetation. They can support the integration of observations into models and improve estimates of vegetation conditions, photosynthesis and carbon uptake.</p>',
                'materials' => ['Deliverables', 'Infographics'],
            ],
            [
                'title' => 'P-model Implementation and User Guidance',
                'slug' => 'p-model-implementation-and-user-guidance',
                'month_label' => 'M30-M36',
                'position' => 'below',
                'position_x' => 55.10,
                'offset_y' => 90,
                'phase' => 'new-earth-observation',
                'categories' => ['earth-observation-community'],
                'description' => 'An improved version of the P-model for estimating how plants absorb carbon through photosynthesis. The model will use satellite information on vegetation characteristics and account more effectively for the influence of temperature, soil moisture and plant structure. Guidance and documentation will support its application by researchers and modelling communities.',
                'relevant_for' => '<p>Vegetation and ecosystem researchers, carbon-cycle scientists, climate modellers, Earth observation specialists and researchers studying ecosystem responses to climate extremes.</p>',
                'how_used' => '<p>The model can support studies of plant productivity, carbon uptake and vegetation responses to environmental stress. It can also be incorporated into land and Earth system models to improve their representation of photosynthesis.</p>',
                'materials' => ['Deliverables', 'User guidance materials'],
            ],
            [
                'title' => 'Multivariate Vegetation Data Assimilation',
                'slug' => 'multivariate-vegetation-data-assimilation',
                'month_label' => 'M32-M42',
                'position' => 'above',
                'position_x' => 62.20,
                'offset_y' => 94,
                'phase' => 'model-integration',
                'categories' => ['climate-modellers', 'climate-services'],
                'description' => 'A new approach for combining several types of satellite-based vegetation information with land and climate models. By using observations of vegetation activity, leaf area and above-ground biomass together, it will improve estimates of vegetation conditions, carbon uptake and exchanges between the land and atmosphere.',
                'relevant_for' => '<p>Climate and Earth system modellers, data-assimilation experts, Earth observation researchers, satellite-mission teams and organisations developing climate forecasting systems.</p>',
                'how_used' => '<p>The approach can improve estimates of vegetation conditions, carbon uptake and exchanges between the land and atmosphere. It can also help identify which combinations of satellite observations provide the greatest improvements to model performance.</p>',
                'materials' => ['Deliverables'],
            ],
            [
                'title' => 'Seasonal Forecast Demonstrators',
                'slug' => 'seasonal-forecast-demonstrators',
                'month_label' => 'M34-M42',
                'position' => 'below',
                'position_x' => 74.40,
                'offset_y' => 60,
                'phase' => 'model-integration',
                'categories' => ['researchers', 'climate-modellers', 'climate-services'],
                'description' => 'A series of seasonal forecasting experiments demonstrating how CONCERTO’s improved land information and modelling methods can influence predictions of climate extremes. The experiments will examine major European heatwaves and compare forecasts produced using existing and improved model configurations.',
                'relevant_for' => '<p>Climate forecasting centres, climate service providers, researchers, public authorities, policymakers and organisations responsible for preparing for climate-related risks.</p>',
                'how_used' => '<p>The demonstrators can help assess whether improved information about vegetation, soil and the carbon cycle leads to more reliable seasonal forecasts. Their findings can guide the further development of forecasting systems and climate services.</p>',
                'materials' => ['Deliverables', 'Policy recommendations', 'Factsheets'],
            ],
            [
                'title' => 'Improved Land Carbon-cycle Models',
                'slug' => 'improved-land-carbon-cycle-models',
                'month_label' => 'M36-M48',
                'position' => 'above',
                'position_x' => 78.60,
                'offset_y' => 38,
                'phase' => 'model-integration',
                'categories' => ['climate-modellers'],
                'description' => 'Improved land and Earth system models that represent the movement of carbon through vegetation, soils, rivers and the atmosphere more accurately. The developments will also strengthen the connections between the carbon cycle and the water, energy and nitrogen cycles, as well as processes such as vegetation growth, fires and emissions.',
                'relevant_for' => '<p>Climate and Earth system modelling centres, carbon-cycle researchers, hydrologists, ecosystem scientists, climate service providers and policymakers relying on climate projections.</p>',
                'how_used' => '<p>The models can support more accurate simulations of carbon storage and exchange under present and future climate conditions. They can also be used to investigate vegetation growth, fires, river carbon transport and ecosystem responses to disturbances.</p>',
                'materials' => ['Deliverables', 'Scientific papers'],
            ],
            [
                'title' => 'Integrated Climate-model Demonstration',
                'slug' => 'integrated-climate-model-demonstration',
                'month_label' => 'M42-M48',
                'position' => 'below',
                'position_x' => 86.70,
                'offset_y' => 93,
                'phase' => 'climate-demonstration',
                'categories' => ['climate-modellers', 'policymakers', 'climate-services'],
                'description' => 'A final demonstration of how CONCERTO’s datasets and model improvements perform when combined in seasonal and long-term climate simulations. It will examine their influence on the representation of carbon uptake, soil moisture, river discharge, vegetation stress and exchanges of carbon, water and heat between the land and atmosphere.',
                'relevant_for' => '<p>Climate modelling and forecasting centres, Earth system researchers, climate service providers, policymakers and organisations using climate information for planning and risk assessment.</p>',
                'how_used' => '<p>The demonstration can provide evidence of how the project’s combined developments affect simulations of carbon uptake, soil moisture, river discharge, vegetation stress and exchanges of carbon, water and heat between the land and atmosphere. The findings can inform future climate-model development and forecasting applications.</p>',
                'materials' => ['Deliverables'],
            ],
        ];
    }
}
