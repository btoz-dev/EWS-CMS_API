<div class="accordion mb-1" id="accordionRole">
    <div class="card">
        <div class="card-header" id="{{ isset($title) ? str_slug($title) :  'permissionHeading' }}">
            <h2 class="mb-0">
                <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#dd-{{ isset($title) ? str_slug($title) :  'permissionHeading' }}" aria-expanded="{{ $closed or 'true' }}" aria-controls="dd-{{ isset($title) ? str_slug($title) :  'permissionHeading' }}">
                    {{ $title or 'Override Permissions' }} {!! isset($user) ? '<span class="text-danger">(' . $user->getDirectPermissions()->count() . ')</span>' : '' !!}
                </button>
            </h2>
        </div>

        <div id="dd-{{ isset($title) ? str_slug($title) :  'permissionHeading' }}" class="collapse show" aria-labelledby="{{ isset($title) ? str_slug($title) :  'permissionHeading' }}" data-parent="#accordionRole">
            <div class="card-body">
                <div class="row">
                    @foreach($permissions as $perm)
                        <?php
                            $per_found = null;

                            if( isset($role) ) {
                                $per_found = $role->hasPermissionTo($perm->name);
                            }

                            if( isset($user)) {
                                $per_found = $user->hasDirectPermission($perm->name);
                            }
                        ?>

                        <div class="col-md-3">
                            <div class="checkbox">
                                <label class="{{ str_contains($perm->name, 'delete') ? 'text-danger' : '' }}">
                                    {!! Form::checkbox("permissions[]", $perm->name, $per_found, isset($options) ? $options : []) !!} {{ $perm->name }}
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>