
<?php
    /** @var \App\Models\TutorFormField $field */
    $key = $field->field_key;
    $label = $field->label;
    $required = $field->is_required;
    $help = $field->help_text;
    $placeholder = $field->placeholder;
    $width = $field->width === 'half' ? 'sm:col-span-1' : 'sm:col-span-2';
    $settings = $field->settings ?? [];
    $opts = $field->resolvedOptions();
    $reqMark = $required ? ' *' : '';
    $oldWeekly = $oldWeekly ?? old('weekly_availability', []);
?>

<?php if($field->field_type === 'info'): ?>
    <div class="<?php echo e($width); ?> rounded-xl bg-sky-50 border border-sky-100 p-4 text-sm text-sky-900">
        <p class="font-bold m-0"><?php echo e($label); ?></p>
        <?php if($help): ?><p class="m-0 mt-1 text-xs"><?php echo e($help); ?></p><?php endif; ?>
    </div>
<?php elseif($field->field_type === 'country_phone'): ?>
    <div class="<?php echo e($width); ?>">
        <label class="ta-label"><?php echo e($label); ?><?php echo e($reqMark); ?></label>
        <div class="grid grid-cols-[7.5rem_1fr] gap-2">
            <select name="country_code" class="ta-field" dir="ltr" <?php if($required): ?> required <?php endif; ?>>
                <?php $__currentLoopData = $phoneCountries ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($c['dial_code']); ?>" <?php if(old('country_code', $defaultCountry['dial_code'] ?? '+966') === $c['dial_code']): echo 'selected'; endif; ?>><?php echo e($c['dial_code']); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <input type="tel" name="phone" class="ta-field flex-1" dir="ltr" value="<?php echo e(old('phone')); ?>" <?php if($required): ?> required <?php endif; ?> placeholder="5xxxxxxxx">
        </div>
        <?php if($help): ?><p class="text-xs text-slate-500 m-0 mt-1"><?php echo e($help); ?></p><?php endif; ?>
    </div>
<?php elseif($field->field_type === 'password'): ?>
    <div class="<?php echo e($width); ?>">
        <label class="ta-label"><?php echo e($label); ?><?php echo e($reqMark); ?></label>
        <input type="password" name="<?php echo e($key); ?>" class="ta-field" <?php if($required): ?> required <?php endif; ?> autocomplete="new-password">
        <?php if($help): ?><p class="text-xs text-slate-500 m-0 mt-1"><?php echo e($help); ?></p><?php endif; ?>
    </div>
<?php elseif($field->field_type === 'textarea'): ?>
    <div class="<?php echo e($width); ?>">
        <label class="ta-label"><?php echo e($label); ?><?php echo e($reqMark); ?></label>
        <textarea name="<?php echo e($key); ?>" class="ta-field" rows="<?php echo e((int) ($settings['rows'] ?? 3)); ?>" <?php if($required): ?> required <?php endif; ?> placeholder="<?php echo e($placeholder); ?>"><?php echo e(old($key)); ?></textarea>
        <?php if($help): ?><p class="text-xs text-slate-500 m-0 mt-1"><?php echo e($help); ?></p><?php endif; ?>
    </div>
<?php elseif(in_array($field->field_type, ['text', 'email', 'tel', 'url', 'number', 'date'], true)): ?>
    <div class="<?php echo e($width); ?>">
        <label class="ta-label"><?php echo e($label); ?><?php echo e($reqMark); ?></label>
        <input type="<?php echo e($field->field_type === 'tel' ? 'tel' : $field->field_type); ?>"
               name="<?php echo e($key); ?>" class="ta-field"
               value="<?php echo e(old($key)); ?>"
               <?php if($required): ?> required <?php endif; ?>
               <?php if($placeholder): ?> placeholder="<?php echo e($placeholder); ?>" <?php endif; ?>
               <?php if(isset($settings['min'])): ?> min="<?php echo e($settings['min']); ?>" <?php endif; ?>
               <?php if(isset($settings['max']) && $field->field_type === 'number'): ?> max="<?php echo e($settings['max']); ?>" <?php endif; ?>
               <?php if($field->field_type === 'url'): ?> dir="ltr" <?php endif; ?>>
        <?php if($help): ?><p class="text-xs text-slate-500 m-0 mt-1"><?php echo e($help); ?></p><?php endif; ?>
    </div>
<?php elseif($field->field_type === 'select'): ?>
    <div class="<?php echo e($width); ?>">
        <label class="ta-label"><?php echo e($label); ?><?php echo e($reqMark); ?></label>
        <select name="<?php echo e($key); ?>" class="ta-field" <?php if($required): ?> required <?php endif; ?>>
            <option value="">— اختر —</option>
            <?php $__currentLoopData = $opts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ov => $ol): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($ov); ?>" <?php if(old($key) == $ov): echo 'selected'; endif; ?>><?php echo e($ol); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <?php if($help): ?><p class="text-xs text-slate-500 m-0 mt-1"><?php echo e($help); ?></p><?php endif; ?>
    </div>
<?php elseif($field->field_type === 'radio'): ?>
    <div class="<?php echo e($width); ?>">
        <p class="ta-label"><?php echo e($label); ?><?php echo e($reqMark); ?></p>
        <div class="ta-check-grid">
            <?php $__currentLoopData = $opts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ov => $ol): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <label class="ta-check-item">
                    <input type="radio" name="<?php echo e($key); ?>" value="<?php echo e($ov); ?>" <?php if(old($key) == $ov): echo 'checked'; endif; ?> <?php if($required): ?> required <?php endif; ?>>
                    <?php echo e($ol); ?>

                </label>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php if($help): ?><p class="text-xs text-slate-500 m-0 mt-1"><?php echo e($help); ?></p><?php endif; ?>
    </div>
<?php elseif(in_array($field->field_type, ['checkbox_group', 'multiselect'], true)): ?>
    <div class="<?php echo e($width); ?>">
        <p class="ta-label"><?php echo e($label); ?><?php echo e($reqMark); ?></p>
        <div class="ta-check-grid" data-tutor-check-group="<?php echo e($key); ?>" <?php if($required): ?> data-required-group="1" <?php endif; ?>>
            <?php $__currentLoopData = $opts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ov => $ol): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <label class="ta-check-item">
                    <input type="checkbox" name="<?php echo e($key); ?>[]" value="<?php echo e($ov); ?>" <?php if(in_array((string) $ov, array_map('strval', old($key, [])), true)): echo 'checked'; endif; ?>>
                    <?php echo e($ol); ?>

                </label>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php if($help): ?><p class="text-xs text-slate-500 m-0 mt-1"><?php echo e($help); ?></p><?php endif; ?>
    </div>
<?php elseif($field->field_type === 'subjects'): ?>
    <div class="<?php echo e($width); ?>">
        <p class="ta-label"><?php echo e($label); ?><?php echo e($reqMark); ?></p>
        <div class="ta-check-grid" data-tutor-check-group="subject_ids" <?php if($required): ?> data-required-group="1" <?php endif; ?>>
            <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <label class="ta-check-item">
                    <input type="checkbox" name="subject_ids[]" value="<?php echo e($s->id); ?>" <?php if(in_array($s->id, old('subject_ids', []), true)): echo 'checked'; endif; ?>>
                    <?php echo e($s->name); ?>

                </label>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
<?php elseif($field->field_type === 'academic_years'): ?>
    <div class="<?php echo e($width); ?>">
        <p class="ta-label"><?php echo e($label); ?><?php echo e($reqMark); ?></p>
        <div class="ta-check-grid" data-tutor-check-group="academic_year_ids" <?php if($required): ?> data-required-group="1" <?php endif; ?>>
            <?php $__currentLoopData = $years; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $y): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <label class="ta-check-item">
                    <input type="checkbox" name="academic_year_ids[]" value="<?php echo e($y->id); ?>" <?php if(in_array($y->id, old('academic_year_ids', []), true)): echo 'checked'; endif; ?>>
                    <?php echo e($y->name); ?>

                </label>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
<?php elseif($field->field_type === 'file'): ?>
    <div class="<?php echo e($width); ?>">
        <label class="ta-label"><?php echo e($label); ?><?php echo e($reqMark); ?></label>
        <input type="file" name="<?php echo e($key); ?>" class="ta-field"
               <?php if($required): ?> required <?php endif; ?>
               <?php if(!empty($settings['accept'])): ?> accept="<?php echo e($settings['accept']); ?>" <?php endif; ?>>
        <?php if($help): ?><p class="text-xs text-slate-500 m-0 mt-1"><?php echo e($help); ?></p><?php endif; ?>
    </div>
<?php elseif($field->field_type === 'weekly_availability'): ?>
    <div class="<?php echo e($width); ?> space-y-2">
        <p class="ta-label m-0"><?php echo e($label); ?><?php echo e($reqMark); ?></p>
        <?php if($help): ?><p class="text-sm text-slate-600 m-0"><?php echo e($help); ?></p><?php endif; ?>
        <div class="overflow-x-auto rounded-xl border border-slate-200">
            <table class="w-full text-sm">
                <thead class="bg-slate-50"><tr><th class="p-2 text-right">اليوم</th><th class="p-2 text-right">الفترات</th><th class="p-2 text-right">ملاحظات</th></tr></thead>
                <tbody>
                <?php $__currentLoopData = $formOptions['weekdays'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day => $dayLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr class="border-t border-slate-100">
                    <td class="p-2 font-bold whitespace-nowrap"><?php echo e($dayLabel); ?></td>
                    <td class="p-2"><input type="text" name="weekly_availability[<?php echo e($day); ?>][periods]" class="ta-field text-xs" placeholder="مثال: 4–8 م" value="<?php echo e($oldWeekly[$day]['periods'] ?? ''); ?>"></td>
                    <td class="p-2"><input type="text" name="weekly_availability[<?php echo e($day); ?>][notes]" class="ta-field text-xs" placeholder="—" value="<?php echo e($oldWeekly[$day]['notes'] ?? ''); ?>"></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
<?php elseif($field->field_type === 'video_pair'): ?>
    <?php echo $__env->make('tutor.partials.field-video-pair', ['field' => $field, 'required' => $required], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php elseif($field->field_type === 'commitments'): ?>
    <div class="<?php echo e($width); ?> space-y-2">
        <p class="ta-label"><?php echo e($label); ?><?php echo e($reqMark); ?></p>
        <?php $__currentLoopData = $opts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ck => $ct): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <label class="ta-check-item">
                <input type="checkbox" name="commitments[<?php echo e($ck); ?>]" value="1" <?php if(old('commitments.'.$ck)): echo 'checked'; endif; ?> <?php if($required): ?> required <?php endif; ?>>
                <?php echo e($ct); ?>

            </label>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
<?php elseif($field->field_type === 'declaration'): ?>
    <div class="<?php echo e($width); ?> space-y-3">
        <label class="ta-check-item">
            <input type="checkbox" name="declaration_agreed" value="1" <?php if(old('declaration_agreed')): echo 'checked'; endif; ?> <?php if($required): ?> required <?php endif; ?>>
            أقرّ بأن جميع البيانات صحيحة وأوافق على سياسات الأكاديمية
        </label>
        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="ta-label">الاسم<?php echo e($reqMark); ?></label>
                <input type="text" name="declaration_name" class="ta-field" value="<?php echo e(old('declaration_name')); ?>" <?php if($required): ?> required <?php endif; ?>>
            </div>
            <div>
                <label class="ta-label">التوقيع<?php echo e($reqMark); ?></label>
                <input type="text" name="declaration_signature" class="ta-field" value="<?php echo e(old('declaration_signature')); ?>" <?php if($required): ?> required <?php endif; ?> placeholder="اكتب اسمك كتوقيع">
            </div>
        </div>
    </div>
<?php elseif($field->field_type === 'matching_modes'): ?>
    <div class="<?php echo e($width); ?>">
        <p class="ta-label"><?php echo e($label); ?><?php echo e($reqMark); ?></p>
        <div class="ta-check-grid" data-tutor-check-group="matching_modes" <?php if($required): ?> data-required-group="1" <?php endif; ?>>
            <label class="ta-check-item"><input type="checkbox" name="matching_modes[]" value="pick_teacher" <?php if(in_array('pick_teacher', old('matching_modes', []), true)): echo 'checked'; endif; ?>> <?php echo e(__('tutor.matching_pick_teacher')); ?></label>
            <label class="ta-check-item"><input type="checkbox" name="matching_modes[]" value="self_schedule" <?php if(in_array('self_schedule', old('matching_modes', []), true)): echo 'checked'; endif; ?>> <?php echo e(__('tutor.matching_self_schedule')); ?></label>
            <label class="ta-check-item"><input type="checkbox" name="matching_modes[]" value="assisted" <?php if(in_array('assisted', old('matching_modes', []), true)): echo 'checked'; endif; ?>> <?php echo e(__('tutor.matching_assisted')); ?></label>
        </div>
    </div>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\sana\resources\views/tutor/partials/field-renderer.blade.php ENDPATH**/ ?>