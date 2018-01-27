var gulp = require('gulp');

var minifyCss = require('gulp-minify-css');         //- 压缩CSS文件；
var rev = require('gulp-rev');                      //- 对css、js文件名加MD5后缀
var revCollector = require('gulp-rev-collector');   //- 路径替换
var clean = require('gulp-clean');                  //- 用于删除文件
var uglify = require('gulp-uglify');                //- 压缩js代码
var replace = require('gulp-replace');              //- 替换文件内容

/*清理文件*/
gulp.task('clean',function () {                     //删除dist目录下的所有文件
    return gulp.src(['public/dist', 'rev'], {read:false})  //必须加return，不然会碰到类似Error: ENOENT: no such file or directory错误
        .pipe(clean());
});

/*压缩js文件，并生成md5后缀的js文件*/
gulp.task('compress-js', function (callback) {       //- 创建一个名为compress-js的task
    gulp.src(['public/js/**/*.js', 'public/js/*.js'], {base: 'public'})             //- 需要处理的js文件，放到一个字符串数组里
        .pipe(uglify())                             //- 压缩文件
        .pipe(rev())                                //- 文件名加MD5后缀
        .pipe(gulp.dest('public/dist'))                   //- 另存压缩后的文件
        .pipe(rev.manifest('manifest-js.json'))                       //- 生成一个manifest-js.json
        .pipe(gulp.dest('rev'))                  //- 将manifest-js.json保存到rev目录内
        .pipe(replace(/:\s*"/g, ': "dist/'))     //替换manifest-js.json文件中新资源路径
        .pipe(gulp.dest('rev'))
        .on('end',function () {
            console.log('compress-js has been completed');
            callback();
        });
});

/*压缩css文件，并生成md5后缀的css文件*/
gulp.task('compress-css', function(callback) {      //- 创建一个名为compress-css的task
    gulp.src(['public/css/*.css'], {base: 'public'})           //- 需要处理的css文件，放到一个字符串数组里
        .pipe(minifyCss())                          //- 压缩处理成一行
        .pipe(rev())                                //- 文件名加MD5后缀
        .pipe(gulp.dest('public/dist'))                    //- 输出文件到dist目录下 跟原目录一致
        .pipe(rev.manifest('manifest-css.json'))                       //- 生成一个manifest-css.json
        .pipe(gulp.dest('rev'))                 //- 将manifest-css.json保存到rev目录内
        .pipe(replace(/:\s*"/g, ': "dist/'))     //替换manifest-css.json文件中新资源路径
        .pipe(gulp.dest('rev'))
        .on('end',function () {
            console.log('compress-css has been completed');
            callback();
        });
});

/*修改html文件的link标签和script标签引用的css和js文件名，并把html文件输出到指定的位置*/
gulp.task('rev-html',['compress-css','compress-js'], function() {          //- compress-css和compress-js任务执行完毕再执行rev-index任务
    /*修改index.html文件的link标签和script标签引用的css和js文件名，并把html文件输出到指定的位置*/
    gulp.src(['rev/*.json', 'application/views/scripts/**/*.phtml'])   //- 读取manifest.json文件以及需要进行css和js名替换的html文件
        .pipe(revCollector())                                               //- 执行文件内css和js名的替换
        .pipe(gulp.dest('application/views/scripts'));                                          //- 替换后的html文件输出的目录
    gulp.src(['rev/*.json', 'application/layout/scripts/*.phtml'])
        .pipe(revCollector())
        .pipe(gulp.dest('application/layout/scripts'));
    gulp.src(['rev/*.json', 'application/modules/crawler/views/scripts/**/*.phtml'])
        .pipe(revCollector())
        .pipe(gulp.dest('application/modules/crawler/views/scripts'));
    gulp.src(['rev/*.json', 'application/modules/person/views/scripts/**/*.phtml'])
        .pipe(revCollector())
        .pipe(gulp.dest('application/modules/person/views/scripts'));
});


//不可这样写，这些任务是同步的，完全可能出现边编译边删除的情况，如Error: ENOENT: no such file or directory, stat ‘......’
//gulp.task('default',['clean','compress-js', 'compress-css', 'rev-html']);
gulp.task('default', ['clean'], function(){
    gulp.start('compress-js', 'compress-css', 'rev-html');
});