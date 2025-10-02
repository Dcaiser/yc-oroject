<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class FixReportsUiCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:reports-ui';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix UI consistency issues across all report pages';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔧 Fixing Reports UI consistency...');
        
        $reportFiles = [
            'movement.blade.php',
            'supplier-performance.blade.php', 
            'weekly.blade.php'
        ];
        
        foreach ($reportFiles as $file) {
            $this->fixReportFile($file);
        }
        
        $this->info('✅ All report pages UI fixed successfully!');
        
        return 0;
    }
    
    private function fixReportFile($filename)
    {
        $filepath = resource_path("views/reports/{$filename}");
        
        if (!File::exists($filepath)) {
            $this->warn("File not found: {$filename}");
            return;
        }
        
        $content = File::get($filepath);
        
        // Fix header section
        $content = $this->fixHeaderSection($content);
        
        // Fix main container
        $content = $this->fixMainContainer($content);
        
        // Fix filter sections
        $content = $this->fixFilterSections($content);
        
        // Fix summary cards
        $content = $this->fixSummaryCards($content);
        
        // Fix table responsiveness
        $content = $this->fixTableResponsiveness($content);
        
        File::put($filepath, $content);
        
        $this->line("  ✓ Fixed {$filename}");
    }
    
    private function fixHeaderSection($content)
    {
        // Fix header wrapper
        $content = preg_replace(
            '/<div class="flex items-center justify-between">/',
            '<div class="reports-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">',
            $content
        );
        
        // Fix title size
        $content = preg_replace(
            '/class="font-semibold text-xl text-gray-800 leading-tight">/',
            'class="font-semibold text-lg sm:text-xl text-gray-800 leading-tight">',
            $content
        );
        
        // Fix export buttons wrapper
        $content = preg_replace(
            '/<div class="flex space-x-2">/',
            '<div class="btn-group no-print">',
            $content
        );
        
        // Fix export button classes
        $content = preg_replace(
            '/class="bg-(red|green)-600 hover:bg-(red|green)-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200">/',
            'class="btn btn-secondary bg-$1-600 hover:bg-$2-700 text-white px-4 py-2.5 rounded-lg text-sm font-medium transition-colors duration-200">',
            $content
        );
        
        return $content;
    }
    
    private function fixMainContainer($content)
    {
        return preg_replace(
            '/<div class="space-y-6">/',
            '<div class="space-y-6 reports-main">',
            $content
        );
    }
    
    private function fixFilterSections($content)
    {
        // Add filter-section class
        $content = preg_replace(
            '/<div class="bg-white overflow-hidden shadow-sm (sm:)?rounded-lg">(\s*<div class="p-6">.*?<h3.*?>.*?Filter.*?<\/h3>)/s',
            '<div class="bg-white overflow-hidden shadow-sm rounded-lg filter-section">$2',
            $content
        );
        
        return $content;
    }
    
    private function fixSummaryCards($content)
    {
        // Fix summary grid
        $content = preg_replace(
            '/<div class="grid grid-cols-1 md:grid-cols-4 gap-6">/',
            '<div class="summary-grid">',
            $content
        );
        
        return $content;
    }
    
    private function fixTableResponsiveness($content)
    {
        // Fix table wrapper
        $content = preg_replace(
            '/<div class="overflow-x-auto">/',
            '<div class="table-responsive">',
            $content
        );
        
        return $content;
    }
}
