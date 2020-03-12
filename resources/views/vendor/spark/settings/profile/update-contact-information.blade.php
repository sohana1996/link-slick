<spark-update-contact-information :user="user" inline-template>
    <div class="panel panel-default">
        <div class="panel-heading">Contact Information</div>

        <div class="panel-body">
            <!-- Success Message -->
            <div class="alert alert-success" v-if="form.successful">
                Your contact information has been updated!
            </div>

            <form class="form-horizontal" role="form">
                <!-- Name -->
                <div class="form-group" :class="{'has-error': form.errors.has('name')}">
                    <label class="col-md-4 control-label">Name</label>

                    <div class="col-md-6">
                        <input type="text" class="form-control" name="name" v-model="form.name">

                        <span class="help-block" v-show="form.errors.has('name')">
                            @{{ form.errors.get('name') }}
                        </span>
                    </div>
                </div>

                <!-- E-Mail Address -->
                <div class="form-group" :class="{'has-error': form.errors.has('email')}">
                    <label class="col-md-4 control-label">E-Mail Address</label>

                    <div class="col-md-6">
                        <input type="email" class="form-control" name="email" v-model="form.email">

                        <span class="help-block" v-show="form.errors.has('email')">
                            @{{ form.errors.get('email') }}
                        </span>
                    </div>
                </div>

                <!-- Timezone Info -->
                <div class="form-group" :class="{'has-error': form.errors.has('tz')}">
                    <label class="col-md-4 control-label">Timezone</label>

                    <div class="col-md-6">
                        <select class="form-control" name="tz" v-model="form.tz">
                            <option value="0">Select Timezone</option>
                            @foreach($timezone as $tz)
                                <option value="{{$tz['id']}}">
                                    @if($tz['offset'] > 0) + @endif {{$tz['offset']}} {{$tz['title']}}
                                </option>
                            @endforeach
                        </select>

                        <span class="help-block" v-show="form.errors.has('tz')">
                            @{{ form.errors.get('tz') }}
                        </span>
                    </div>
                </div>

                <!-- Update Button -->
                <div class="form-group">
                    <div class="col-md-offset-4 col-md-6">
                        <button type="submit" class="btn btn-primary"
                                @click.prevent="update"
                                :disabled="form.busy">

                            Update
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</spark-update-contact-information>
