<?php
namespace App\Providers;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Builder;
class JsonApiPaginateServiceProvider extends ServiceProvider
{
    public function boot()
    {

        $this->registerMacro();
    }
    public function register()
    {
    }
    protected function registerMacro()
    {
        Builder::macro(config('json-api-paginate.method_name'), function (int $size = null, $col=['*'],$pageName='page',$page=null,$baseUrl=null) {
            $defaultSize = $defaultSize ?? config('json-api-paginate.default_size');

            $size = !is_null($size)?$size:$defaultSize;
            $paginator = $this
                ->paginate($size,$col,$pageName,$page)
//                ->setPageName($paginationParameter.'['.$numberParameter.']')
                ->appends(Arr::except(request()->input(), 'page'));

            if (! is_null($baseUrl)) {
                $paginator->setPath($baseUrl);
            }
            return $paginator;
        });
    }
}
