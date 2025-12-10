<?php
namespace App\Http\Services\Users;

use App\Models\Design;
use App\Models\Measurement;
use App\Models\DesignOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Exceptions\GeneralException;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DesignService
{
    public function allDesigns($data)
    {
        $designs = Design::filter($data)
        ->with(['user', 'designImages', 'measurements'])
        ->paginate($data['per_page'] ?? 15);
        return $designs;
    }
    public function myDesigns($data)
    {
        $user = User::find(Auth::id());
        return $user->designs()->filterUser($data)
            ->with('designImages', 'measurements', 'designOptions')
            ->paginate($data['per_page'] ?? 15);
    }

    public function store($data)
    {
        return DB::transaction(function () use ($data) {
            // التحقق من الصور
            if (empty($data['images'])) {
                throw new GeneralException("Store Design failed: no images provided", 400);
            }

            // إنشاء التصميم
            $design = Design::create([
                'name' => $data['name'],
                'description' => $data['description'],
                'price' => $data['price'],
                'user_id' => Auth::id(),
            ]);

            // حفظ الصور
            $this->handleImages($design, $data['images']);

            // معالجة المقاسات
            $this->syncMeasurements($design, $data['measurements']);

            // معالجة خيارات التصميم
            $this->syncDesignOptions($design, $data['design_options']);

            return $design->load('designImages', 'measurements', 'designOptions');
        });
    }

    public function update($design, $data)
    {
        return DB::transaction(function () use ($design, $data) {
            // تحديث بيانات التصميم الأساسية
            $design->update([
                'name' => $data['name'] ?? $design->name,
                'description' => $data['description'] ?? $design->description,
                'price' => $data['price'] ?? $design->price,
            ]);

            // تحديث الصور إذا تم إرسالها
            if (!empty($data['images'])) {
                $this->deleteOldImages($design);
                $this->handleImages($design, $data['images']);
            }

            // تحديث المقاسات إذا تم إرسالها
            if (!empty($data['measurements'])) {
                $this->syncMeasurements($design, $data['measurements']);
            }

            // تحديث خيارات التصميم إذا تم إرسالها
            if (!empty($data['design_options'])) {
                $this->syncDesignOptions($design, $data['design_options']);
            }

            return $design->load('designImages', 'measurements', 'designOptions');
        });
    }

    public function destroy($design)
    {
        DB::transaction(function () use ($design) {
            // حذف الصور المرتبطة
            $this->deleteOldImages($design);

            // حذف العلاقات مع المقاسات وخيارات التصميم
            $design->measurements()->detach();
            $design->designOptions()->detach();

            // حذف التصميم نفسه
            $design->delete();
        });
    }

    /**
     * حفظ صور التصميم
     */
    private function handleImages(Design $design, array $images): void
    {
        foreach ($images as $image) {
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('design_images', $imageName, 'public');
            $design->designImages()->create(['image_path' => $imagePath]);
        }
    }

    /**
     * حذف الصور القديمة
     */
    private function deleteOldImages(Design $design): void
    {
        foreach ($design->designImages as $designImage) {
            if (Storage::disk('public')->exists($designImage->image_path)) {
                Storage::disk('public')->delete($designImage->image_path);
            }
        }
        $design->designImages()->delete();
    }

    /**
     * مزامنة المقاسات مع التصميم
     */
    private function syncMeasurements(Design $design, array $measurementSizes): void
    {
        $measurements = Measurement::whereIn('size', $measurementSizes)->pluck('id');

        if ($measurements->isEmpty()) {
            throw new GeneralException("No valid measurements found", 400);
        }

        $design->measurements()->sync($measurements);
    }

    /**
     * مزامنة خيارات التصميم
     */
    private function syncDesignOptions(Design $design, array $designOptions): void
    {
        $optionTypes = $designOptions['type'] ?? [];
        $optionNamesEn = $designOptions['name']['en'] ?? [];
        $optionNamesAr = $designOptions['name']['ar'] ?? [];

        $designOptionIds = $this->findDesignOptionIds($optionTypes, $optionNamesEn, $optionNamesAr);

        if (empty($designOptionIds)) {
            throw new GeneralException("No valid design options found", 400);
        }

        
        $design->designOptions()->sync($designOptionIds);
    }

    /**
     * البحث عن معرفات خيارات التصميم
     */
    private function findDesignOptionIds(array $types, array $namesEn, array $namesAr): array
    {
        $designOptionIds = [];

        foreach ($types as $index => $type) {
            $query = DesignOption::where('type', $type);

            if (!empty($namesEn[$index])) {
                $query->where('name->en', $namesEn[$index]);
            }

            if (!empty($namesAr[$index])) {
                $query->where(function ($q) use ($namesAr, $index) {
                    $q->orWhere('name->ar', $namesAr[$index]);
                });
            }

            $designOption = $query->first();

            if ($designOption) {
                $designOptionIds[] = $designOption->id;
            }
        }

        return $designOptionIds;
    }
}
