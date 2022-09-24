<?php

declare(strict_types=1);

namespace Rahul900day\Csv;

use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use League\Csv\Reader;
use League\Csv\Statement;

class Builder
{
    protected Statement $statement;

    public function __construct(protected Reader $csv_reader)
    {
        $this->statement = Statement::create();
    }

    public function offset(int $value): static
    {
        $this->statement = $this->statement->offset($value);

        return $this;
    }

    public function limit(int $value): static
    {
        $this->statement = $this->statement->limit($value);

        return $this;
    }

    public function forPage(int $page, int $perPage = 15): static
    {
        return $this->offset(($page - 1) * $perPage)->limit($perPage);
    }

    public function get($columns = []): Collection
    {
        return Collection::make(new RecordList($this->statement->process($this->csv_reader, $columns)));
    }

    public function lazy(int $chunkSize = 1000): LazyCollection
    {
        return LazyCollection::make(function () use ($chunkSize) {
            $page = 1;

            while (true) {
                $results = $this->forPage($page++, $chunkSize)->get();

                foreach ($results as $result) {
                    yield $result;
                }

                if ($results->count() < $chunkSize) {
                    return;
                }
            }
        });
    }

    public function chunk(int $count, callable $callback): bool
    {
        $page = 1;

        do {
            // We'll execute the query for the given page and get the results. If there are
            // no results we can just break and return from here. When there are results
            // we will call the callback with the current chunk of these results here.
            $results = $this->forPage($page, $count)->get();

            $countResults = $results->count();

            if ($countResults == 0) {
                break;
            }

            // On each chunk result set, we will pass them to the callback and then let the
            // developer take care of everything within the callback, which allows us to
            // keep the memory low for spinning through large result sets for working.
            if ($callback($results, $page) === false) {
                return false;
            }

            unset($results);

            $page++;
        } while ($countResults == $count);

        return true;
    }
}
