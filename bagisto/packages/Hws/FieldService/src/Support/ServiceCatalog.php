<?php

namespace Hws\FieldService\Support;

use Illuminate\Support\Collection;

class ServiceCatalog
{
    public static function all(): Collection
    {
        return collect([
            self::service('water-treatment-plants', 'Water Treatment Plants (WTP)', 'Water Treatment & Purification', 'water-treatment-hero.webp',
                'Engineered treatment trains that convert raw water into reliable process, utility or potable-quality water.',
                'We design WTPs around your source-water report, daily demand, operating hours and end-use quality—not a generic equipment list.',
                ['Industries and factories', 'Hotels, hospitals and institutions', 'Housing and commercial campuses'],
                ['Raw-water analysis and process selection', 'Clarification, filtration and disinfection', 'Controls, dosing and instrumentation', 'Installation, commissioning and operator training'],
                ['Consistent outlet-water quality', 'Lower operating and chemical cost', 'Expandable, service-friendly plant layout']
            ),
            self::service('reverse-osmosis-plants', 'Reverse Osmosis (RO) Plants', 'Water Treatment & Purification', 'water-treatment-hero.webp',
                'High-recovery RO plants sized for your feed-water chemistry, flow demand and product-water specification.',
                'From pretreatment and membrane selection to automation and CIP planning, every stage is matched to real operating conditions.',
                ['Industrial process water', 'Commercial drinking water', 'Boiler, cooling and production utilities'],
                ['Feed-water and recovery assessment', 'Pretreatment and antiscalant design', 'Membrane, pump and vessel selection', 'PLC controls, flushing and CIP provision'],
                ['Stable permeate quality', 'Improved membrane life', 'Reduced water and energy wastage']
            ),
            self::service('sewage-treatment-plants', 'Sewage Treatment Plants (STP)', 'Wastewater Treatment', 'wastewater-hero.webp',
                'Compact, compliant STP solutions for safe sewage treatment and practical reuse in flushing, gardening and utilities.',
                'We plan the biological process, equalisation, clarification, tertiary treatment and sludge handling as one operable system.',
                ['Residential societies and townships', 'Hotels, schools and hospitals', 'Commercial and institutional campuses'],
                ['Flow and pollution-load assessment', 'MBBR, SBR or suitable biological process', 'Tertiary filtration and disinfection', 'Sludge handling and reuse integration'],
                ['Reliable treated-water reuse', 'Reduced freshwater demand', 'Simple operations and maintenance']
            ),
            self::service('effluent-treatment-plants', 'Effluent Treatment Plants (ETP)', 'Wastewater Treatment', 'wastewater-hero.webp',
                'Custom ETPs for variable industrial effluent, discharge compliance and maximum feasible water recovery.',
                'Treatment chemistry and process selection are based on actual pollutants, production cycles and reuse or discharge targets.',
                ['Food and beverage units', 'Textile, chemical and process industries', 'Hospitals and specialised facilities'],
                ['Effluent characterisation and treatability review', 'Physico-chemical and biological treatment', 'Advanced filtration and polishing', 'Sludge dewatering and safe handling'],
                ['Compliance-focused treatment', 'Lower environmental risk', 'Reuse-ready options where feasible']
            ),
            self::service('industrial-water-purification-systems', 'Industrial Water Purification Systems', 'Water Treatment & Purification', 'water-treatment-hero.webp',
                'Integrated purification skids for demanding production, rinse, boiler, cooling and utility applications.',
                'We combine filtration, softening, RO, UF, demineralisation or disinfection into a coordinated system with measurable output.',
                ['Manufacturing and process plants', 'Pharma, food and beverage', 'Power, boiler and cooling utilities'],
                ['Application and water-balance study', 'Multi-stage process engineering', 'Instrumentation and quality monitoring', 'Modular skid fabrication and integration'],
                ['Process-consistent water', 'Reduced scaling and downtime', 'Documented operating parameters']
            ),
            self::service('commercial-residential-water-filtration', 'Commercial & Residential Water Filtration Systems', 'Water Treatment & Purification', 'water-treatment-hero.webp',
                'Practical filtration for buildings, kitchens, institutions and homes—selected around local water quality and consumption.',
                'Solutions range from sediment and carbon filtration to UV, UF and RO, with accessible maintenance and honest capacity sizing.',
                ['Homes and apartments', 'Restaurants, offices and retail', 'Schools, clinics and hospitality'],
                ['Water-quality and usage assessment', 'Central or point-of-use design', 'Food-grade storage and distribution guidance', 'Scheduled filter and consumable plan'],
                ['Better taste, clarity and confidence', 'Right-sized equipment', 'Easy routine maintenance']
            ),
            self::service('water-softeners', 'Water Softeners', 'Water Treatment & Purification', 'water-treatment-hero.webp',
                'Automatic softening systems that control hardness, protect equipment and reduce scale-related maintenance.',
                'Resin volume, regeneration frequency and salt consumption are calculated from hardness, flow and daily water demand.',
                ['Boilers and heat exchangers', 'Hotels, laundries and hospitals', 'Homes and commercial buildings'],
                ['Hardness load and peak-flow calculation', 'Vessel, valve and resin selection', 'Automatic regeneration programming', 'Brine system and bypass arrangement'],
                ['Reduced scale formation', 'Longer appliance and equipment life', 'Predictable salt and water use']
            ),
            self::service('solar-water-heating-systems', 'Solar Water Heating Systems', 'Hot Water & Energy Systems', 'heating-service-hero.webp',
                'Solar hot-water systems engineered for dependable daily output, safe storage and lower conventional energy use.',
                'Collector area, tank capacity, circulation and backup are planned around occupancy, weather, roof and demand pattern.',
                ['Hotels, hostels and hospitals', 'Housing and institutions', 'Industrial canteens and process preheating'],
                ['Demand and solar-availability study', 'Collector and insulated tank sizing', 'Piping, circulation and backup integration', 'Rooftop installation and commissioning'],
                ['Lower water-heating energy cost', 'Reliable hot-water availability', 'Durable, maintainable installation']
            ),
            self::service('heat-pump-water-heating-systems', 'Heat Pump Water Heating Systems', 'Hot Water & Energy Systems', 'heating-service-hero.webp',
                'High-efficiency heat-pump systems for round-the-clock commercial and industrial hot-water demand.',
                'We size the heat pump, storage, circulation and electrical requirements as a complete system for the actual load profile.',
                ['Hotels, hospitals and hostels', 'Commercial kitchens and laundries', 'Industrial hot-water applications'],
                ['Hourly load and recovery calculation', 'Heat-pump and storage selection', 'Hydraulic and control integration', 'Performance testing and handover'],
                ['Reduced electrical consumption', 'Year-round controlled heating', 'Lower lifecycle operating cost']
            ),
            self::service('industrial-hot-water-systems', 'Industrial Hot Water Systems', 'Hot Water & Energy Systems', 'heating-service-hero.webp',
                'Safe, controlled hot-water generation and distribution for process, cleaning and production requirements.',
                'We integrate heating source, insulated storage, pumps, recirculation, controls and safety devices for stable delivery.',
                ['Food and beverage processing', 'Pharmaceutical and manufacturing', 'Cleaning, washdown and process utilities'],
                ['Temperature and demand profiling', 'Heating and storage system design', 'Insulated distribution and recirculation', 'Safety controls and commissioning'],
                ['Stable process temperature', 'Reduced heat loss', 'Safer, better-controlled operation']
            ),
            self::service('installation-commissioning', 'Installation & Commissioning', 'Project & Lifecycle Support', 'heating-service-hero.webp',
                'Disciplined installation, testing and handover so your plant performs as designed from day one.',
                'Our field team coordinates positioning, piping, electricals, controls, dry checks, wet trials and operator handover.',
                ['New HWS projects', 'Third-party water systems', 'Plant upgrades and relocations'],
                ['Site-readiness and layout verification', 'Mechanical and electrical installation', 'Trial run and performance checks', 'Documentation and operator training'],
                ['Faster, safer startup', 'Verified operating parameters', 'Clear handover and maintenance plan']
            ),
            self::service('annual-maintenance-contracts', 'Annual Maintenance Contracts (AMC)', 'Project & Lifecycle Support', 'heating-service-hero.webp',
                'Preventive maintenance plans that reduce surprises, preserve performance and make service costs predictable.',
                'AMC scope is matched to plant criticality, operating hours, consumables and response requirements.',
                ['RO, WTP, STP and ETP plants', 'Softeners and filtration systems', 'Solar, heat-pump and hot-water systems'],
                ['Scheduled preventive visits', 'Performance and quality checks', 'Consumable and spares planning', 'Service reports and recommendations'],
                ['Reduced unplanned downtime', 'Longer equipment life', 'Traceable maintenance history']
            ),
            self::service('technical-support-after-sales', 'Technical Support & After-Sales Service', 'Project & Lifecycle Support', 'heating-service-hero.webp',
                'Responsive troubleshooting, optimisation and parts support throughout the working life of your system.',
                'Our team begins with operating data and symptoms, then provides remote guidance or schedules a site visit as required.',
                ['Existing HWS customers', 'Operators needing troubleshooting', 'Plants needing optimisation or upgrades'],
                ['Remote technical diagnosis', 'Breakdown and corrective service', 'Spares and consumable support', 'Performance optimisation and upgrades'],
                ['Faster issue resolution', 'Better operating confidence', 'One accountable support channel']
            ),
        ]);
    }

    public static function grouped(): Collection
    {
        return self::all()->groupBy('group');
    }

    public static function find(string $slug): ?array
    {
        return self::all()->firstWhere('slug', $slug);
    }

    private static function service(
        string $slug,
        string $title,
        string $group,
        string $image,
        string $summary,
        string $intro,
        array $applications,
        array $inclusions,
        array $outcomes
    ): array {
        return compact('slug', 'title', 'group', 'image', 'summary', 'intro', 'applications', 'inclusions', 'outcomes');
    }
}
