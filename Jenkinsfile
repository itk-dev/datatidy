@Library('jenkins-pipeline')_

pipeline {
    agent any
    stages {
        stage ('Install') {
            parallel {
                stage('Composer') {
                    steps {
                        sh 'docker run -v $WORKSPACE:/app -v /var/lib/jenkins/.composer-cache:/.composer:rw itkdev/php7.3-fpm:latest composer install'
                    }
                }
                stage('Yarn') {
                    steps {
                        sh 'docker run -v $WORKSPACE:/app -v /var/lib/jenkins/.yarn-cache:/usr/local/share/.cache/yarn:rw itkdev/yarn:latest install'
                    }
                }
            }
        }
        stage('Build and test') {
          parallel {
              stage('PHP') {
                stages {
                  stage('PHP7 compatibility') {
                    steps {
                      sh 'docker run -v $WORKSPACE:/app -v /var/lib/jenkins/.composer-cache:/.composer:rw itkdev/php7.3-fpm:latest vendor/bin/phan --allow-polyfill-parser'
                    }
                  }
                  stage('Coding standards') {
                      steps {
                          sh 'docker run -v $WORKSPACE:/app -v /var/lib/jenkins/.composer-cache:/.composer:rw itkdev/php7.3-fpm:latest composer check-coding-standards'
                          sh 'docker run -v $WORKSPACE:/app -v /var/lib/jenkins/.composer-cache:/.composer:rw itkdev/php7.3-fpm:latest composer check-coding-standards/twigcs'
                      }
                  }
                  stage('Tests') {
                      steps {
                          sh 'docker run -v $WORKSPACE:/app -v /var/lib/jenkins/.composer-cache:/.composer:rw itkdev/php7.3-fpm:latest bin/phpunit'
                      }
                  }
                }
            }
            stage('Assets') {
                    stages {
                        stage('Coding standards') {
                            steps {
                                sh 'docker run -v $WORKSPACE:/app -v /var/lib/jenkins/.yarn-cache:/usr/local/share/.cache/yarn:rw itkdev/yarn:latest check-coding-standards'
                            }
                        }
                        stage('Build') {
                            steps {
                                sh 'docker run -v $WORKSPACE:/app -v /var/lib/jenkins/.yarn-cache:/usr/local/share/.cache/yarn:rw itkdev/yarn:latest build'
                            }
                        }
                    }
                }
            }
        }
        stage('Deployment develop') {
            when {
                branch 'develop'
            }
            steps {
                sh 'echo "DEPLOY"'
            }
        }
        stage('Deployment staging') {
            when {
                branch 'release'
            }
            steps {
                sh 'echo "DEPLOY STAGING"'
            }
        }
        stage('Deployment production') {
            when {
                branch 'master'
            }
            steps {
                timeout(time: 30, unit: 'MINUTES') {
                    input 'Should the site be deployed?'
                }
                sh 'echo "DEPLOY PRODUCTION"'
            }
        }
    }
    post {
        always {
            script {
                slackNotifier(currentBuild.currentResult)
            }
        }
    }
}
