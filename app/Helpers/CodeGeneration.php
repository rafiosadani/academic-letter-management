<?php

namespace App\Helpers;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CodeGeneration {
    private string $model;
    private string $column;
    private string $prefix;
    private string $code;

    public function __construct(string $model, string $column, string $prefix)
    {
        $this->model  = $model;
        $this->column = $column;
        $this->prefix = $prefix;

        $this->generate();
    }

    public function getGeneratedCode(): string
    {
        return $this->code;
    }

    private function generate(): void
    {
        $year  = date("y"); // contoh: 25
        $month = date("m"); // contoh: 11

        $last = $this->getLastCodeRecord();

        if ($last) {

            $parts = explode("/", $last->{$this->column});
            // format: PREFIX/2501/0001
            // Extract last month from code
            $lastMonth = substr($parts[1], 2, 2);

            // Bulan sama → increment nomor
            if ($lastMonth === $month) {
                // Increase sequence
                $lastNumber = (int)$parts[2] + 1;
                $sequence = str_pad($lastNumber, 4, "0", STR_PAD_LEFT);
            } else {
                // Reset sequence if month changed
                $sequence = "0001";
            }
        } else {
            // First generated code ever
            $sequence = "0001";
        }

        $this->code = "{$this->prefix}/{$year}{$month}/{$sequence}";
    }

    private function getLastCodeRecord()
    {
        return $this->model::select($this->column)
            ->whereMonth("created_at", Carbon::now()->month)
            ->whereYear("created_at", Carbon::now()->year)
            ->where(DB::raw("SUBSTR({$this->column}, 1, 3)"), $this->prefix)
            ->orderByDesc($this->column)
            ->first();
    }
}