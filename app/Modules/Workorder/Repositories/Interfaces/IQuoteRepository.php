<?php
namespace App\Modules\Workorder\Repositories\Interfaces;

use App\Modules\Workorder\Models\Quote;
use Illuminate\Support\Collection;

interface IQuoteRepository
{
    /**
     * @return
     */
    public function list();

    /**
     * @param int $id
     * @return Quote
     */
    public function get(int $id): Quote;

    /**
     * @param array $data
     * @return Quote
     */
    public function store(array $data): Quote;

    /**
     * @param int $id
     * @param array $data
     * @return Quote
     */
    public function update(int $id, array $data);

    /**
     * @param int $id
     * @return Quote
     */
    public function delete(int $id): Quote;
}
