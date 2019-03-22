@servers(['aws' => 'ubuntu@18.218.17.126'])
//@servers(['aws' => '-i ~/<pem> ubuntu@18.218.17.126'])

@include('vendor/autoload.php')

@setup
    $origin = 'git@github.com:ctacuri/sistema';
    $branch = isset($branch) ? $branch : 'master';
    $app_dir = '/opt/lampp/htdocs';

    if ( !isset($on) ) {
        throw new Exception('La variable --on no esta definida');
    }
@endsetup

@task('git:clone', ['on' => $on])
    cd {{ $app_dir }}
    echo "hemos entrado al directorio /opt/lampp/htdocs"
    git clone {{ $origin }}
    echo "repositorio clonado correctamente"
@endtask

@task('ls', ['on' => $on])
    cd {{ $app_dir }}
    ls -la
@endtask